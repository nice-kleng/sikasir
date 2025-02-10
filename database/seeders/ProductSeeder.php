<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $products = [
            [
                'nama_menu' => 'Nasi Goreng Special',
                'harga' => 25000,
                'foto' => 'nasi-goreng.jpg',
                'deskripsi' => 'Nasi goreng dengan telur, ayam, dan sayuran'
            ],
            [
                'nama_menu' => 'Mie Goreng',
                'harga' => 20000,
                'foto' => 'mie-goreng.jpg',
                'deskripsi' => 'Mie goreng dengan telur dan sayuran'
            ],
            [
                'nama_menu' => 'Ayam Bakar',
                'harga' => 30000,
                'foto' => 'ayam-bakar.jpg',
                'deskripsi' => 'Ayam bakar dengan sambal dan lalapan'
            ],
            [
                'nama_menu' => 'Es Teh Manis',
                'harga' => 5000,
                'foto' => 'es-teh.jpg',
                'deskripsi' => 'Teh manis dengan es batu'
            ],
            [
                'nama_menu' => 'Juice Alpukat',
                'harga' => 15000,
                'foto' => 'juice-alpukat.jpg',
                'deskripsi' => 'Juice alpukat segar dengan susu'
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
