<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;

class CustomerOrderController extends Controller
{
    public function index()
    {
        // Ambil kategori produk
        $categories = Kategori::all();

        // Ambil produk yang tersedia
        $products = Product::where('status', 'Tersedia')->paginate(10);

        return view('customer.order', [
            'categories' => $categories,
            'products' => $products
        ]);
    }

    public function getProductsByCategory($categoryId)
    {
        $products = Product::where('kategori_id', $categoryId)
            ->where('status', 'Tersedia')
            ->get();

        return response()->json($products);
    }

    public function orderSuccess($transactionId)
    {
        $transaction = Transaction::findOrFail($transactionId);
        return view('customer.order-success', compact('transaction'));
    }
}
