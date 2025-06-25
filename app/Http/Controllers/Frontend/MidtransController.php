<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class MidtransController extends Controller
{
    public function callback(Request $request)
    {
        $payload = $request->all();
        Log::info('Midtrans Callback:', $payload);

        $orderIdRaw = $payload['order_id']; // format: 12-64b232a
        $statusCode = $payload['status_code'];
        $transactionStatus = $payload['transaction_status'];
        $fraudStatus = $payload['fraud_status'];

        // Ambil ID dari order_id
        $orderId = explode('-', $orderIdRaw)[0];

        $order = Order::findOrFail($orderId);

        if ($transactionStatus === 'capture' || $transactionStatus === 'settlement') {
            $order->update(['payment_status' => 'paid', 'status' => 'processing']);
        } elseif ($transactionStatus === 'pending') {
            $order->update(['payment_status' => 'pending']);
        } elseif (in_array($transactionStatus, ['expire', 'cancel', 'deny'])) {
            $order->update(['payment_status' => 'failed', 'status' => 'cancelled']);
        }

        return response()->json(['message' => 'Callback received']);
    }
}
