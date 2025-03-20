<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ProductSeeder extends Seeder
{
    public function run()
    {
        Kategori::create([
            'nama_kategori' => 'Minuman',
            'deskripsi' => 'Dekripsi minuman',
        ]);
        Kategori::create([
            'nama_kategori' => 'Makanan',
            'deskripsi' => 'Dekripsi makanan',
        ]);

        $products = [
            [
                'nama_menu' => 'Nasi Goreng Special',
                'harga' => 25000,
                'foto' => 'images/nasi-goreng.jpg',
                'deskripsi' => 'Nasi goreng dengan telur, ayam, dan sayuran',
                'kategori_id' => 2,
                'status' => 'Tersedia',
            ],
            [
                'nama_menu' => 'Mie Goreng',
                'harga' => 20000,
                'foto' => 'images/mie-goreng.jpg',
                'deskripsi' => 'Mie goreng dengan telur dan sayuran',
                'kategori_id' => 2,
                'status' => 'Tersedia',
            ],
            [
                'nama_menu' => 'Ayam Bakar',
                'harga' => 30000,
                'foto' => 'images/ayam-bakar.jpg',
                'deskripsi' => 'Ayam bakar dengan sambal dan lalapan',
                'kategori_id' => 2,
                'status' => 'Tersedia',
            ],
            [
                'nama_menu' => 'Es Teh Manis',
                'harga' => 5000,
                'foto' => 'images/es-teh.jpg',
                'deskripsi' => 'Teh manis dengan es batu',
                'kategori_id' => 1,
                'status' => 'Tersedia',
            ],
            [
                'nama_menu' => 'Juice Alpukat',
                'harga' => 15000,
                'foto' => 'images/juice-alpukat.jpg',
                'deskripsi' => 'Juice alpukat segar dengan susu',
                'kategori_id' => 1,
                'status' => 'Tersedia',
            ],
            [
                'nama_menu' => 'Jus Jeruk',
                'harga' => 10000,
                'foto' => 'images/orange-juice.jpg',
                'deskripsi' => 'Juice jeruk segar dengan susu dan madu',
                'kategori_id' => 1,
                'status' => 'Tersedia',
            ],
            [
                'nama_menu' => 'Lemon Tea',
                'harga' => 8000,
                'foto' => 'images/lemon-tea.jpg',
                'deskripsi' => 'Lemon Tea Sehat Menyegarkan',
                'kategori_id' => 1,
                'status' => 'Tersedia',
            ],
            [
                'nama_menu' => 'Boba Milk Tea',
                'harga' => 18000,
                'foto' => 'images/boba-milk-tea.jpg',
                'deskripsi' => 'Lemon Tea Sehat Menyegarkan',
                'kategori_id' => 1,
                'status' => 'Tersedia',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
