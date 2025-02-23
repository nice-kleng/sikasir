<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;

class TransactionSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('id_ID');
        $products = Product::all();

        // Generate data untuk 1 bulan terakhir saja
        $startDate = Carbon::now()->subMonth()->startOfDay();
        $endDate = Carbon::now()->endOfDay();
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            // Generate 3-8 transaksi per hari
            $transactionsPerDay = $faker->numberBetween(3, 8);

            for ($i = 0; $i < $transactionsPerDay; $i++) {
                // Beri jeda waktu minimal 15 menit antar transaksi
                $transactionTime = $currentDate->copy()
                    ->addHours($faker->numberBetween(10, 22))
                    ->addMinutes($faker->numberBetween(0, 59));

                // Generate invoice dengan microseconds
                $microtime = sprintf('%06d', $faker->numberBetween(0, 999999));
                $invoiceNumber = sprintf(
                    'INV/%s/%s',
                    $transactionTime->format('Ymd'),
                    $microtime
                );

                $transaction = Transaction::create([
                    'nomor_invoice' => $invoiceNumber,
                    'total_pembayaran' => 0, // Initialize with 0
                    'total_pajak' => 0,      // Initialize with 0
                    'payment_method' => $faker->randomElement(['cash', 'online']),
                    'user_id' => 1,
                    'payment_status' => 'paid',
                    'created_at' => $transactionTime,
                    'updated_at' => $transactionTime,
                    'snap_token' => null,
                    'midtrans_transaction_id' => null,
                    'midtrans_payment_type' => null,
                ]);

                // Buat 1-3 items per transaksi
                $totalPembayaran = 0;
                $itemCount = $faker->numberBetween(1, 3);

                for ($j = 0; $j < $itemCount; $j++) {
                    $product = $products->random();
                    $qty = $faker->numberBetween(1, 2);
                    $subtotal = $qty * $product->harga;

                    TransactionItem::create([
                        'transaction_id' => $transaction->id,
                        'product_id' => $product->id,
                        'jumlah' => $qty,
                        'harga' => $product->harga,
                        'subtotal' => $subtotal,
                        'discount' => 0,
                        'created_at' => $transactionTime,
                        'updated_at' => $transactionTime,
                    ]);

                    $totalPembayaran += $subtotal;
                }

                $pajak = round($totalPembayaran * 0.1);
                $transaction->update([
                    'total_pembayaran' => $totalPembayaran,
                    'total_pajak' => $pajak
                ]);
            }

            $currentDate->addDay();
        }
    }
}
