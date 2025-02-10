<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class TransactionSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('id_ID');
        $products = Product::all();

        // Create 20 sample transactions
        for ($i = 0; $i < 20; $i++) {
            $transaction = Transaction::create([
                'nomor_invoice' => 'INV-' . date('Ymd') . $faker->unique()->numberBetween(100, 999),
                'total_pembayaran' => 0, // Will be calculated later
                'total_pajak' => 0, // Will be calculated later
                'payment_method' => $faker->randomElement(['cash', 'card', 'qris']),
                'user_id' => $faker->randomElement([1, 2]),
                'payment_status' => 'paid',
                'snap_token' => null,
                'midtrans_transaction_id' => null,
                'midtrans_payment_type' => null,
            ]);

            // Create 1-5 items for each transaction
            $totalPembayaran = 0;
            $itemCount = $faker->numberBetween(1, 5);

            for ($j = 0; $j < $itemCount; $j++) {
                $product = $products->random();
                $qty = $faker->numberBetween(1, 3);
                $harga = $product->harga;
                $subtotal = $qty * $harga;
                $discount = $faker->randomElement([0, 5000, 10000]);
                $subtotal -= $discount;

                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'jumlah' => $qty,
                    'harga' => $harga,
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'notes' => $faker->optional()->sentence(),
                ]);

                $totalPembayaran += $subtotal;
            }

            // Update transaction totals
            $pajak = round($totalPembayaran * 0.1); // 10% tax
            $transaction->update([
                'total_pembayaran' => $totalPembayaran,
                'total_pajak' => $pajak
            ]);
        }
    }
}
