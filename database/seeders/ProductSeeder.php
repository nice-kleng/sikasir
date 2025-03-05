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
                'deskripsi' => 'Nasi goreng dengan telur, ayam, dan sayuran',
                'kategori' => 'Makanan'
            ],
            [
                'nama_menu' => 'Mie Goreng',
                'harga' => 20000,
                'foto' => 'mie-goreng.jpg',
                'deskripsi' => 'Mie goreng dengan telur dan sayuran',
                'kategori' => 'Makanan'
            ],
            [
                'nama_menu' => 'Ayam Bakar',
                'harga' => 30000,
                'foto' => 'ayam-bakar.jpg',
                'deskripsi' => 'Ayam bakar dengan sambal dan lalapan',
                'kategori' => 'Makanan'
            ],
            [
                'nama_menu' => 'Es Teh Manis',
                'harga' => 5000,
                'foto' => 'es-teh.jpg',
                'deskripsi' => 'Teh manis dengan es batu',
                'kategori' => 'Minuman'
            ],
            [
                'nama_menu' => 'Juice Alpukat',
                'harga' => 15000,
                'foto' => 'juice-alpukat.jpg',
                'deskripsi' => 'Juice alpukat segar dengan susu',
                'kategori' => 'Minuman'
            ],
            [
                'nama_menu' => 'Jus Jeruk',
                'harga' => 10000,
                'foto' => 'orange-juice.jpg',
                'deskripsi' => 'Juice jeruk segar dengan susu dan madu',
                'kategori' => 'Minuman'
            ],
            [
                'nama_menu' => 'Lemon Tea',
                'harga' => 8000,
                'foto' => 'lemon-tea.jpg',
                'deskripsi' => 'Lemon Tea Sehat Menyegarkan',
                'kategori' => 'Minuman'
            ],
            [
                'nama_menu' => 'Boba Milk Tea',
                'harga' => 18000,
                'foto' => 'lemon-tea.jpg',
                'deskripsi' => 'Lemon Tea Sehat Menyegarkan',
                'kategori' => 'Minuman'
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
