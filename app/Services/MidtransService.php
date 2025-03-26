<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {
        $settings = Setting::first();

        if ($settings) {
            Config::$serverKey = $settings->midtrans_server_key;
            Config::$clientKey = $settings->midtrans_client_key;
            Config::$isProduction = $settings->midtrans_environment === 'production';
        }
    }

    public function createTransaction($order)
    {
        $params = [
            'transaction_details' => [
                'order_id' => $order->nomor_invoice,
                'gross_amount' => $order->total_pembayaran,
            ],
            'customer_details' => [
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email,
            ],
            'item_details' => $this->formatItems($order->items),
            'enabled_payments' => [
                'credit_card',
                'bca_va',
                'bni_va',
                'bri_va',
                'mandiri_clickpay',
                'gopay',
                'shopeepay'
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            return $snapToken;
        } catch (\Exception $e) {
            report($e);
            return null;
        }
    }

    private function formatItems($items)
    {
        return $items->map(function ($item) {
            return [
                'id' => $item->product_id,
                'price' => $item->harga,
                'quantity' => $item->jumlah,
                'name' => $item->product->nama_menu,
            ];
        })->toArray();
    }
}
