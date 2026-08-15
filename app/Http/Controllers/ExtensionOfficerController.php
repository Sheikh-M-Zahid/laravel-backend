<?php

namespace App\Http\Controllers;

use App\Models\AdvisoryMessage;
use App\Models\Alert;
use App\Models\ClimateZone;
use App\Models\Crop;
use App\Models\FarmProfile;
use App\Models\OfficerOverride;
use App\Models\OfficerVerification;
use App\Models\OfficerZoneAssignment;
use App\Models\Recommendation;
use App\Models\RecommendationFeedback;
use App\Models\TrainingSession;
use App\Models\User;
use App\Services\BrevoMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExtensionOfficerController extends Controller
{
    public function dashboard()
    {
        $officerId = Auth::id();
        $assignedZoneIds = OfficerZoneAssignment::where('extension_officer_id', $officerId)->pluck('zone_id');

        $pendingProfiles = FarmProfile::where('verification_status', 'pending')
            ->when($assignedZoneIds->isNotEmpty(), fn ($q) => $q->whereIn('zone_id', $assignedZoneIds))
            ->with('farmer', 'zone')->latest()->get();

        $recentFeedback = RecommendationFeedback::with(['farmer', 'recommendation.recommendedCrop'])
            ->latest()->take(15)->get();

        // For the Zone ID / Farmer dropdowns below (instead of raw ID text fields).
        $zones = ClimateZone::orderBy('zone_name')->get();
        $farmers = User::where('role', 'farmer')->orderBy('name')->get();

        return view('officer.dashboard', compact('pendingProfiles', 'recentFeedback', 'zones', 'farmers'));
    }

    /** Verify Soil / Farm Data Submitted use case — emails the farmer either way */
    public function verifyFarmProfile(Request $request, FarmProfile $farmProfile, BrevoMailService $brevo)
    {
        $data = $request->validate([
            'status' => ['required', 'in:verified,rejected'],
            'notes' => ['nullable', 'string'],
        ]);

        OfficerVerification::create([
            'farm_profile_id' => $farmProfile->id,
            'extension_officer_id' => Auth::id(),
            'status' => $data['status'],
            'notes' => $data['notes'] ?? null,
        ]);
        $farmProfile->update(['verification_status' => $data['status']]);

        $farmer = $farmProfile->farmer;
        $verb = $data['status'] === 'verified' ? 'verified ✅' : 'rejected ❌';
        $body = "<p>Your Extension Officer has <strong>{$verb}</strong> plot #{$farmProfile->id}.</p>";
        if (! empty($data['notes'])) {
            $body .= "<p>Note: " . e($data['notes']) . "</p>";
        }
        $brevo->notifyUser(
            $farmer,
            "Your farm profile was {$data['status']} — Smart Agri-Advisory Platform",
            "Farm profile #{$farmProfile->id} {$verb}",
            $body,
            'View my dashboard',
            route('farmer.dashboard')
        );

        return back()->with('status', 'Farm profile marked as ' . $data['status'] . '.');
    }

    /** Review / Override ML Crop Recommendation use case (<<extend>>) — emails the farmer */
    public function overrideRecommendation(Request $request, Recommendation $recommendation, BrevoMailService $brevo)
    {
        $data = $request->validate([
            'overridden_crop_name' => ['required', 'string'],
            'reason' => ['required', 'string'],
        ]);
        $crop = Crop::findByNameOrCreate($data['overridden_crop_name']);

        OfficerOverride::create([
            'recommendation_id' => $recommendation->id,
            'extension_officer_id' => Auth::id(),
            'overridden_crop_id' => $crop->id,
            'reason' => $data['reason'],
        ]);

        $farmer = $recommendation->farmer;
        $brevo->notifyUser(
            $farmer,
            'An Extension Officer reviewed your recommendation — Smart Agri-Advisory Platform',
            "Recommendation #{$recommendation->id} was updated",
            "<p>Your Extension Officer suggests <strong>{$crop->crop_name}</strong> instead, based on local conditions.</p>"
                . "<p>Reason: " . e($data['reason']) . "</p>",
            'View recommendation history',
            route('farmer.history')
        );

        return back()->with('status', 'Override recorded for this recommendation.');
    }

    /** Provide Advisory Feedback to Farmer use case — emails the farmer */
    public function sendAdvisory(Request $request, BrevoMailService $brevo)
    {
        $data = $request->validate([
            'farmer_id' => ['required', 'exists:users,id'],
            'message' => ['required', 'string'],
        ]);

        AdvisoryMessage::create([
            'extension_officer_id' => Auth::id(),
            'farmer_id' => $data['farmer_id'],
            'message' => $data['message'],
            'sent_at' => now(),
        ]);

        $farmer = User::find($data['farmer_id']);
        $officerName = Auth::user()->name;
        $brevo->notifyUser(
            $farmer,
            "New advisory message from {$officerName} — Smart Agri-Advisory Platform",
            "Your Extension Officer sent you advice",
            "<p>" . nl2br(e($data['message'])) . "</p>",
            'View my dashboard',
            route('farmer.dashboard')
        );

        return back()->with('status', 'Advisory message sent to farmer.');
    }

    /** Send Pest / Weather Alerts to Farmers use case — emails + in-app notifies every farmer with a plot in the zone */
    public function sendAlert(Request $request, BrevoMailService $brevo)
    {
        $data = $request->validate([
            'zone_id' => ['required', 'exists:climate_zones,id'],
            'alert_type' => ['required', 'in:pest,weather'],
            'message' => ['required', 'string'],
        ]);

        Alert::create([
            'extension_officer_id' => Auth::id(),
            'zone_id' => $data['zone_id'],
            'alert_type' => $data['alert_type'],
            'message' => $data['message'],
            'sent_at' => now(),
        ]);

        $zone = ClimateZone::find($data['zone_id']);
        $farmers = User::where('role', 'farmer')
            ->whereHas('farmProfiles', fn ($q) => $q->where('zone_id', $data['zone_id']))
            ->get();

        $icon = $data['alert_type'] === 'pest' ? '🐛' : '🌦️';
        foreach ($farmers as $farmer) {
            $brevo->notifyUser(
                $farmer,
                "{$icon} " . ucfirst($data['alert_type']) . " alert for {$zone->zone_name} — Smart Agri-Advisory Platform",
                ucfirst($data['alert_type']) . " alert — {$zone->zone_name}",
                "<p>" . nl2br(e($data['message'])) . "</p>",
                'View my dashboard',
                route('farmer.dashboard')
            );
        }

        return back()->with('status', "Alert broadcast to farmers in {$zone->zone_name}" . ($farmers->isEmpty() ? ' (no farmers with a plot there yet).' : '.'));
    }

    /** Schedule Farmer Training Sessions use case */
    public function scheduleTraining(Request $request)
    {
        $data = $request->validate([
            'zone_id' => ['required', 'exists:climate_zones,id'],
            'title' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'session_date' => ['required', 'date'],
            'location' => ['nullable', 'string'],
        ]);
        $data['extension_officer_id'] = Auth::id();

        TrainingSession::create($data);

        return back()->with('status', 'Training session scheduled.');
    }

    /** Monitor Regional Crop Trends use case */
    public function regionalTrends()
    {
        $trends = Recommendation::join('crops', 'crops.id', '=', 'recommendations.recommended_crop_id')
            ->selectRaw('crops.crop_name as crop, count(*) as total')
            ->groupBy('crops.crop_name')
            ->orderByDesc('total')
            ->get();

        return view('officer.trends', compact('trends'));
    }
}
