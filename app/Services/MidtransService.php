<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Midtrans\Config;
use Midtrans\Snap;


class MidtransService
{
    public function __construct($serverKey, $clientKey, $isProduction)
    {
        Config::$serverKey = $serverKey;
        Config::$clientKey = $clientKey;
        Config::$isProduction = $isProduction;
        // Config::$merchantId = config('midtrans.merchant_id');
        // Config::$serverKey = config('midtrans.server_key');
        // Config::$clientKey = config('midtrans.client_key');
        // Config::$isProduction = config('midtrans.is_production', false);
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
