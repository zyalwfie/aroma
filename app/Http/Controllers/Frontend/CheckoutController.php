<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class CheckoutController extends Controller
{
    public function show(Request $request)
    {
        $cartItems = auth()->user()->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang Anda kosong.');
        }

        $user = auth()->user()->load('addresses');
        return view('frontend.product.checkout', compact('user', 'cartItems'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'shipping_method' => 'required|in:jne,pos,tiki',
            'payment_method' => 'required|in:midtrans,cod',
            'terms' => 'accepted',
        ]);

        $user = auth()->user();
        $cartItems = $user->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang Anda kosong.');
        }

        $address = $user->addresses()->findOrFail($request->address_id);
        $total = $cartItems->sum(fn($item) => $item->product->price * $item->quantity);

        $shippingCost = match ($request->shipping_method) {
            'jne' => 20000,
            'pos' => 25000,
            'tiki' => 18000,
            default => 20000,
        };
        $grandTotal = $total + $shippingCost;

        $midtransOrderId = $user->id . '-' . time();

        $order = $user->orders()->create([
            'name' => $user->name,
            'phone' => $user->phone,
            'address' => $address->address,
            'total' => $grandTotal,
            'status' => 'pending',
            'payment_method' => $request->payment_method,
            'shipping_method' => $request->shipping_method,
            'midtrans_order_id' => $midtransOrderId,
        ]);

        foreach ($cartItems as $item) {
            $order->orderItems()->create([
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price,
            ]);
            $item->product->decrement('stock', $item->quantity);
            $item->delete();
        }

        if ($request->payment_method === 'midtrans') {
            try {
                Config::$serverKey = config('services.midtrans.server_key');
                Config::$isProduction = config('services.midtrans.is_production');
                Config::$isSanitized = true;
                Config::$is3ds = true;

                $params = [
                    'transaction_details' => [
                        'order_id' => $midtransOrderId,
                        'gross_amount' => $grandTotal,
                    ],
                    'customer_details' => [
                        'first_name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'billing_address' => [
                            'address' => $address->address,
                            'city' => $address->city ?? 'Unknown',
                            'postal_code' => $address->zip ?? '00000',
                            'country_code' => 'IDN',
                        ],
                    ],
                    'item_details' => $cartItems->map(function ($item) {
                        return [
                            'id' => $item->product_id,
                            'price' => $item->product->price,
                            'quantity' => $item->quantity,
                            'name' => $item->product->name,
                        ];
                    })->toArray(),
                ];

                $params['item_details'][] = [
                    'id' => 'SHIPPING',
                    'price' => $shippingCost,
                    'quantity' => 1,
                    'name' => 'Biaya Pengiriman',
                ];

                $snap = Snap::createTransaction($params);
                $paymentUrl = $snap->redirect_url;

                $order->update([
                    'snap_token' => $snap->token,
                    'payment_url' => $paymentUrl,
                ]);

                return redirect($paymentUrl);

            } catch (\Exception $e) {
                Log::error('Midtrans Error: ' . $e->getMessage());
                return back()->with('error', 'Gagal terhubung ke Midtrans.');
            }
        }

        return redirect()->route('home')->with('success', 'Pesanan berhasil dibuat.');
    }

   public function midtransCallback(Request $request)
{
    $notif = new \Midtrans\Notification();

    $transaction = $notif->transaction_status;
    $type = $notif->payment_type;
    $fraud = $notif->fraud_status;
    $orderIdRaw = $notif->order_id;

    // Pecah order ID → ambil ID order asli dari yang dikirim Midtrans
    $orderId = explode('-', $orderIdRaw)[0];

    $order = Order::find($orderId);

    if (!$order) {
        Log::error('Order not found for Midtrans callback: ' . $orderId);
        return response()->json(['message' => 'Order not found'], 404);
    }

    // Update status
    if ($transaction == 'capture' || $transaction == 'settlement') {
        $order->update(['payment_status' => 'paid', 'status' => 'paid']);
    } elseif ($transaction == 'pending') {
        $order->update(['payment_status' => 'pending']);
    } elseif (in_array($transaction, ['deny', 'expire', 'cancel'])) {
        $order->update(['payment_status' => 'failed', 'status' => 'cancelled']);
    }

    return response()->json(['message' => 'Callback received'], 200);
}
}
