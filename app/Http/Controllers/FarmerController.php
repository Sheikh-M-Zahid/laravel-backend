<?php

namespace App\Http\Controllers;

use App\Models\ClimateZone;
use App\Models\Crop;
use App\Models\DiseaseDetection;
use App\Models\FarmProfile;
use App\Models\FertilizerRecommendation;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PriceForecast;
use App\Models\Product;
use App\Models\Recommendation;
use App\Models\RecommendationFeedback;
use App\Models\WeatherLog;
use App\Services\MlService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FarmerController extends Controller
{
    public function dashboard()
    {
        $farmProfiles = Auth::user()->farmProfiles()->with('zone')->get();
        $zones = ClimateZone::all();
        return view('farmer.dashboard', compact('farmProfiles', 'zones'));
    }

    /** Add Farm Profile (Land, Soil pH, NPK) use case — profile + soil data are one table in this schema */
    public function storeFarmProfile(Request $request)
    {
        $data = $request->validate([
            'zone_id' => ['required', 'exists:climate_zones,id'],
            'land_size_acres' => ['required', 'numeric', 'min:0.01'],
            'location_text' => ['nullable', 'string'],
            'soil_ph' => ['required', 'numeric', 'between:3,10'],
            'nitrogen' => ['required', 'numeric', 'min:0'],
            'phosphorus' => ['required', 'numeric', 'min:0'],
            'potassium' => ['required', 'numeric', 'min:0'],
        ]);
        $data['user_id'] = Auth::id();
        $data['verification_status'] = 'pending';

        Auth::user()->farmProfiles()->create($data);

        return redirect()->route('farmer.dashboard')->with('status', 'Farm profile saved. An Extension Officer will verify it.');
    }

    /** Get Crop Recommendation use case (Random Forest, includes Fetch Weather) */
    public function recommendCrop(Request $request, MlService $ml)
    {
        $request->validate(['farm_profile_id' => ['required', 'exists:farm_profiles,id']]);
        $profile = FarmProfile::with('zone')->where('user_id', Auth::id())->findOrFail($request->farm_profile_id);

        // Fetch Weather & Climate Zone Data use case: use the most recent
        // cached weather_logs row for this zone if present, else sensible defaults.
        $weather = WeatherLog::where('zone_id', $profile->zone_id)->latest('fetched_at')->first();

        $result = $ml->recommendCrop([
            'soil_ph' => (float) $profile->soil_ph,
            'nitrogen' => (float) $profile->nitrogen,
            'phosphorus' => (float) $profile->phosphorus,
            'potassium' => (float) $profile->potassium,
            'rainfall_mm' => (float) ($weather->rainfall ?? 150),
            'temperature_c' => (float) ($weather->temperature ?? 27),
            'humidity_pct' => (float) ($weather->humidity ?? 70),
        ]);

        $crop = Crop::findByNameOrCreate($result['top_crop']);
        $topConfidence = collect($result['recommendations'])->firstWhere('crop', $result['top_crop'])['confidence'] ?? 0;

        $recommendation = Recommendation::create([
            'farmer_id' => Auth::id(),
            'farm_profile_id' => $profile->id,
            'recommended_crop_id' => $crop->id,
            'confidence_score' => round($topConfidence * 100, 2),
            'model_version' => 'v1',
        ]);

        return back()->with('crop_result', $result)->with('crop_recommendation_id', $recommendation->id);
    }

    /** Get Fertilizer Recommendation use case (<<extend>> of crop recommendation) */
    public function recommendFertilizer(Request $request, MlService $ml)
    {
        $data = $request->validate([
            'recommendation_id' => ['required', 'exists:recommendations,id'],
        ]);
        $recommendation = Recommendation::with(['farmProfile', 'recommendedCrop'])
            ->where('farmer_id', Auth::id())->findOrFail($data['recommendation_id']);
        $profile = $recommendation->farmProfile;

        $result = $ml->recommendFertilizer($recommendation->recommendedCrop->crop_name, [
            'soil_ph' => (float) $profile->soil_ph,
            'nitrogen' => (float) $profile->nitrogen,
            'phosphorus' => (float) $profile->phosphorus,
            'potassium' => (float) $profile->potassium,
        ]);

        foreach ($result['recommended_dosage_kg_per_acre'] ?? [] as $type => $dosage) {
            FertilizerRecommendation::create([
                'recommendation_id' => $recommendation->id,
                'fertilizer_type' => $type,
                'dosage_kg_per_acre' => $dosage,
                'notes' => $result['method'] ?? null,
            ]);
        }

        return back()->with('fertilizer_result', $result);
    }

    /** View Market Price Forecast use case (LSTM/sequence model) */
    public function priceForecast(Request $request, MlService $ml)
    {
        $data = $request->validate(['crop' => ['required', 'string']]);
        $result = $ml->forecastPrice($data['crop']);

        if (! isset($result['error'])) {
            $crop = Crop::findByNameOrCreate($data['crop']);
            $zone = Auth::user()->farmProfiles()->first()?->zone_id ?? ClimateZone::first()?->id;
            if ($zone) {
                foreach ($result['forecast_bdt_per_kg'] as $i => $price) {
                    PriceForecast::create([
                        'crop_id' => $crop->id,
                        'zone_id' => $zone,
                        'forecast_date' => now()->addMonths($i + 1)->startOfMonth(),
                        'predicted_price' => $price,
                        'model_version' => 'v1',
                    ]);
                }
            }
        }

        return back()->with('price_result', $result);
    }

    /** Upload Image for Pest/Disease Detection use case (CNN, optional advanced module) */
    public function detectPest(Request $request, MlService $ml)
    {
        $data = $request->validate([
            'farm_profile_id' => ['required', 'exists:farm_profiles,id'],
            'image' => ['required', 'image', 'max:5120'],
        ]);
        $path = $request->file('image')->store('pest-uploads', 'local');

        // IMPORTANT: don't hand-build "storage/app/{$path}" -- Laravel 11's
        // default 'local' disk root is storage/app/private, so the real
        // location depends on config/filesystems.php. Storage::path()
        // always resolves it correctly regardless of that config.
        $fullPath = Storage::disk('local')->path($path);

        $result = $ml->detectPest($fullPath);

        DiseaseDetection::create([
            'farmer_id' => Auth::id(),
            'farm_profile_id' => $data['farm_profile_id'],
            'image_path' => $path,
            'detected_disease' => $result['predicted_class'] ?? ($result['status'] ?? 'unknown'),
            'confidence_score' => isset($result['confidence']) ? round($result['confidence'] * 100, 2) : null,
            'suggested_action' => $result['message'] ?? null,
        ]);

        return back()->with('pest_result', $result);
    }

    public function history()
    {
        $recommendations = Recommendation::where('farmer_id', Auth::id())
            ->with(['recommendedCrop', 'farmProfile.zone', 'fertilizerRecommendation'])
            ->latest()->paginate(20);
        return view('farmer.history', compact('recommendations'));
    }

    /** Order Input from Supplier use case */
    public function placeOrder(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);
        $product = Product::findOrFail($data['product_id']);

        $order = Order::create([
            'farmer_id' => Auth::id(),
            'supplier_id' => $product->supplier_id,
            'order_status' => 'pending',
            'total_amount' => $product->price * $data['quantity'],
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => $data['quantity'],
            'unit_price' => $product->price,
        ]);

        return back()->with('status', 'Order placed with ' . $product->supplier->business_name);
    }

    /** Give Feedback (rating/comment) on a Recommendation use case */
    public function sendFeedback(Request $request)
    {
        $data = $request->validate([
            'recommendation_id' => ['required', 'exists:recommendations,id'],
            'rating' => ['required', 'in:helpful,unhelpful'],
            'comment' => ['nullable', 'string'],
        ]);

        RecommendationFeedback::create([
            'recommendation_id' => $data['recommendation_id'],
            'farmer_id' => Auth::id(),
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
        ]);

        return back()->with('status', 'Feedback submitted. Your Extension Officer may follow up.');
    }

    public function marketplace()
    {
        $items = Product::with('supplier')->where('stock_quantity', '>', 0)->latest()->get();
        return view('farmer.marketplace', compact('items'));
    }

    /** My Orders — shows delivery + payment status and lets the farmer submit a bKash TrxID */
    public function myOrders()
    {
        $orders = Order::where('farmer_id', Auth::id())
            ->with(['supplier', 'items.product'])
            ->latest()->get();
        return view('farmer.orders', compact('orders'));
    }

    /** Farmer submits proof of bKash payment (sender number + TrxID) for an order they placed. */
    public function submitPayment(Request $request, Order $order)
    {
        abort_unless($order->farmer_id === Auth::id(), 403);

        $data = $request->validate([
            'bkash_sender_number' => ['required', 'string', 'max:20'],
            'bkash_trx_id' => ['required', 'string', 'max:50'],
            'amount_paid' => ['required', 'numeric', 'min:0.01'],
        ]);

        $order->update([
            'bkash_sender_number' => $data['bkash_sender_number'],
            'bkash_trx_id' => $data['bkash_trx_id'],
            'amount_paid' => $data['amount_paid'],
            'payment_status' => 'pending_verification',
            'payment_submitted_at' => now(),
        ]);

        return back()->with('status', 'Payment submitted. The supplier will verify your TrxID and confirm.');
    }
}
