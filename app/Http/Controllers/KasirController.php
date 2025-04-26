<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KasirController extends Controller
{
    protected $serverKey;
    protected $baseUrl;

    public function __construct()
    {
        // Ambil konfigurasi dari .env atau config
        $this->serverKey = config('midtrans.server_key');
        $isProduction = config('midtrans.is_production', false);
        $this->baseUrl = $isProduction
            ? 'https://api.midtrans.com'
            : 'https://api.sandbox.midtrans.com';
    }

    public function index()
    {
        $setting = Setting::first();
        return view('kasir.kasir', compact('setting'));
    }

    public function handle(Request $request)
    {
        Log::info('Midtrans Callback:', $request->all());

        $setting = Setting::first();
        $serverKey = $setting->midtrans_server_key;
        // $serverKey = config('midtrans.server_key');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed == $request->signature_key) {
            $transaction = Transaction::where('nomor_invoice', $request->order_id)->firstOrFail();

            $transactionStatus = $request->transaction_status;
            $type = $request->payment_type;
            $orderId = $request->order_id;
            $fraud = $request->fraud_status;

            $paymentStatus = match ($transactionStatus) {
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

    public function printNota($transaction_id)
    {
        $transaction = Transaction::with(['items.product', 'user'])
            ->findOrFail($transaction_id);
        $setting = Setting::first();

        $customPaper = array(0, 0, 226.772, 850.394); // 80mm x 297mm in points
        $pdf = Pdf::loadView('kasir.nota', compact('transaction', 'setting'));
        $pdf->setPaper($customPaper, 'portrait');

        return $pdf->stream('Nota-' . $transaction->nomor_invoice . '.pdf');
    }

    // public function refund()
    // {
    //     $transactionid = 'INV-20250322-6532';
    //     $amount = 2000;
    //     $refundKey = 'ref_' . time() . '_' . $transactionid;

    //     $url = $this->baseUrl . '/v2/' . $transactionid . '/refund';

    //     $response = Http::withBasicAuth($this->serverKey, '')
    //         ->post($url, [
    //             'refund_key' => $refundKey,
    //             'amount' => (float) $amount,
    //             'reason' => 'Customer request'
    //         ]);

    //     return $response->json();
    // }
}
