<?php

namespace App\Http\Controllers;

use App\Models\DemandForecast;
use App\Models\Inquiry;
use App\Models\Order;
use App\Models\Product;
use App\Services\BrevoMailService;
use App\Services\MlService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    public function dashboard()
    {
        $supplier = Auth::user()->supplierProfile;
        $items = $supplier ? $supplier->products()->latest()->get() : collect();
        $orders = $supplier ? $supplier->orders()->with(['farmer', 'items.product'])->latest()->get() : collect();
        $inquiries = $supplier ? Inquiry::where('supplier_id', $supplier->id)->with('farmer', 'product')->latest()->get() : collect();

        return view('supplier.dashboard', compact('items', 'orders', 'inquiries', 'supplier'));
    }

    /** My Products — dedicated page (moved out of the dashboard into its own view) */
    public function myProducts()
    {
        $supplier = Auth::user()->supplierProfile;
        $items = $supplier ? $supplier->products()->latest()->get() : collect();

        return view('supplier.products', compact('items', 'supplier'));
    }

    /** Farmer Orders — Delivery & Payment — dedicated page */
    public function myFarmerOrders()
    {
        $supplier = Auth::user()->supplierProfile;
        $orders = $supplier ? $supplier->orders()->with(['farmer', 'items.product'])->latest()->get() : collect();

        return view('supplier.orders', compact('orders', 'supplier'));
    }

    /** Farmer Inquiries — dedicated page */
    public function myInquiries()
    {
        $supplier = Auth::user()->supplierProfile;
        $inquiries = $supplier ? Inquiry::where('supplier_id', $supplier->id)->with('farmer', 'product')->latest()->get() : collect();

        return view('supplier.inquiries', compact('inquiries', 'supplier'));
    }

    /** Set/update the bKash number farmers should pay to. */
    public function updateBkash(Request $request)
    {
        $data = $request->validate(['bkash_number' => ['required', 'string', 'max:20']]);
        Auth::user()->supplierProfile->update($data);

        return back()->with('status', 'bKash number updated.');
    }

    /** List Agri-Input (Seed/Fertilizer) use case */
    public function storeItem(Request $request)
    {
        $data = $request->validate([
            'product_name' => ['required', 'string'],
            'category' => ['required', 'in:seed,fertilizer'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
        ]);
        $data['supplier_id'] = Auth::user()->supplierProfile->id;

        Product::create($data);

        return back()->with('status', 'Product listed.');
    }

    /** Manage Inventory & Stock use case */
    public function updateStock(Request $request, Product $product)
    {
        $this->authorizeOwnership($product);

        $data = $request->validate([
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);
        $product->update($data);

        return back()->with('status', 'Inventory updated.');
    }

    /** View & Fulfil Farmer Orders use case — emails the farmer on every status change */
    public function fulfilOrder(Request $request, Order $order, BrevoMailService $brevo)
    {
        abort_unless($order->supplier_id === Auth::user()->supplierProfile->id, 403);

        $data = $request->validate(['order_status' => ['required', 'in:confirmed,shipped,completed,cancelled']]);
        $order->update($data);

        $brevo->sendNotification(
            $order->farmer->email,
            "Your order is now '{$data['order_status']}' — Smart Agri-Advisory Platform",
            'Order status updated',
            "<p>Your order #{$order->id} with " . Auth::user()->supplierProfile->business_name
                . " is now <strong>" . ucfirst($data['order_status']) . "</strong>.</p>",
            'View my orders',
            route('farmer.orders')
        );

        return back()->with('status', 'Order status updated.');
    }

    /**
     * Supplier reviews the farmer's submitted bKash sender number + TrxID
     * and either confirms the payment (marks it paid, updates amount_paid)
     * or rejects it (sends the order back to unpaid with a note via email).
     */
    public function verifyPayment(Request $request, Order $order, BrevoMailService $brevo)
    {
        abort_unless($order->supplier_id === Auth::user()->supplierProfile->id, 403);

        $data = $request->validate([
            'decision' => ['required', 'in:confirm,reject'],
            'note' => ['nullable', 'string'],
        ]);

        if ($data['decision'] === 'confirm') {
            $order->update([
                'payment_status' => 'paid',
                'payment_verified_at' => now(),
            ]);
            $brevo->sendNotification(
                $order->farmer->email,
                'Payment confirmed — Smart Agri-Advisory Platform',
                "Your payment for order #{$order->id} is confirmed",
                "<p>TrxID <strong>{$order->bkash_trx_id}</strong> for ৳{$order->amount_paid} has been verified by "
                    . Auth::user()->supplierProfile->business_name . ".</p>"
                    . "<p>Remaining due: ৳{$order->due_amount}</p>",
                'View my orders',
                route('farmer.orders')
            );
        } else {
            $order->update([
                'payment_status' => 'unpaid',
                'bkash_trx_id' => null,
                'bkash_sender_number' => null,
                'amount_paid' => 0,
            ]);
            $brevo->sendNotification(
                $order->farmer->email,
                'Payment could not be verified — Smart Agri-Advisory Platform',
                "Your payment for order #{$order->id} was not verified",
                "<p>" . Auth::user()->supplierProfile->business_name . " could not verify your TrxID.</p>"
                    . ($data['note'] ? "<p>Note: " . e($data['note']) . "</p>" : "")
                    . "<p>Please double-check the number and TrxID and submit again.</p>",
                'View my orders',
                route('farmer.orders')
            );
        }

        return back()->with('status', 'Payment ' . ($data['decision'] === 'confirm' ? 'confirmed.' : 'rejected — farmer notified.'));
    }

    /** Respond to Farmer Inquiries use case (<<extend>> of orders) */
    public function respondInquiry(Request $request, Inquiry $inquiry, BrevoMailService $brevo)
    {
        abort_unless($inquiry->supplier_id === Auth::user()->supplierProfile->id, 403);

        $data = $request->validate(['response' => ['required', 'string']]);
        $inquiry->update($data);

        $brevo->sendNotification(
            $inquiry->farmer->email,
            'A supplier replied to your question — Smart Agri-Advisory Platform',
            Auth::user()->supplierProfile->business_name . ' replied to your inquiry',
            "<p>Your question: " . e($inquiry->message) . "</p>"
                . "<p>Their reply: " . e($data['response']) . "</p>",
            'View marketplace',
            route('farmer.marketplace')
        );

        return back()->with('status', 'Response sent to farmer.');
    }

    /** View Regional Demand Forecast (LSTM) use case */
    public function demandForecast(Request $request, MlService $ml)
    {
        $crop = $request->query('crop', 'rice');
        $result = $ml->forecastPrice($crop, 3);

        $storedForecasts = DemandForecast::with('zone')->latest()->take(10)->get();

        return view('supplier.demand', ['forecast' => $result, 'crop' => $crop, 'storedForecasts' => $storedForecasts]);
    }

    protected function authorizeOwnership(Product $item): void
    {
        abort_unless($item->supplier_id === Auth::user()->supplierProfile->id, 403);
    }
}
