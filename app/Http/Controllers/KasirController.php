<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class KasirController extends Controller
{
    public function index()
    {
        return view('kasir.kasir');
    }

    public function handle(Request $request)
    {
        $serverKey = config('midtrans.server_key');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed == $request->signature_key) {
            $transaction = Transaction::where('nomor_invoice', $request->order_id)->firstOrFail();

            $transactionStatus = $request->transaction_status;
            $type = $request->payment_type;
            $orderId = $request->order_id;
            $fraud = $request->fraud_status;

            $paymentStatus = match($transactionStatus) {
                'capture' => ($type == 'credit_card' && $fraud == 'challenge') ? 'pending' : 'paid',
                'settlement' => 'paid',
                'pending' => 'pending',
                'deny' => 'failed',
                'expire' => 'expired',
                'cancel' => 'cancelled',
                default => 'failed'
            };

            $transaction->update([
                'payment_status' => $paymentStatus,
                'midtrans_transaction_id' => $request->transaction_id,
                'midtrans_payment_type' => $request->payment_type
            ]);

            return response()->json(['status' => 'success']);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Invalid signature'
        ], 400);
    }
}
