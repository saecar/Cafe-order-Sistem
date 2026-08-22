<?php

namespace App\Http\Controllers;

use App\Models\order;
use App\Models\payment;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;

class PaymentController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function transaction(order $order)
    {
        $params = [
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => (int) $order->total_amount,
            ],
            'customer_details' => [
                'first_name' => $order->customer_name,
                'email' => $order->email,
                'phone' => $order->phone,
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        payment::create([
            'order_id' => $order->id,
            'midtrans_order_id' => $order->order_number,
            'gross_amount' => $order->total_amount,
            'transaction_status' => 'pending',
            'snap_token' => $snapToken,
        ]);

        return response()->json(['snap_token' => $snapToken]);
    }

    // fix: method ini sebelumnya gak ada, padahal dipanggil di routes
    public function notification(Request $request)
    {
        $notif = new Notification();

        $order = order::where('order_number', $notif->order_id)->firstOrFail();
        $payment = $order->payment;

        $payment->update([
            'transaction_id' => $notif->transaction_id,
            'payment_type' => $notif->payment_type,
            'transaction_status' => $notif->transaction_status,
            'raw_response' => $notif->getResponse(),
            'paid_at' => $notif->transaction_status === 'settlement' ? now() : null,
        ]);

        match ($notif->transaction_status) {
            'settlement', 'capture' => $order->update(['status' => 'processing']),
            'expire', 'cancel', 'deny' => $order->update(['status' => 'cancelled']),
            default => null,
        };

        return response()->json(['message' => 'OK']);
    }
}