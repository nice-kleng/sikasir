<?php

namespace App\Livewire;

use App\Models\Kategori;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CustomerOrder extends Component
{
    public $cart = [];
    public $nama_konsumen = '';
    public $no_table = '';

    // public $categories;
    // public $products;

    // public function mount($categories, $products)
    // {
    //     $this->categories = $categories;
    //     $this->products = $products;
    // }

    public function addToCart($productId)
    {
        $product = Product::find($productId);

        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['jumlah']++;
        } else {
            $this->cart[$productId] = [
                'nama_menu' => $product->nama_menu,
                'harga' => $product->harga,
                'jumlah' => 1
            ];
        }
    }

    public function removeFromCart($productId)
    {
        unset($this->cart[$productId]);
    }

    public function updateQuantity($productId, $change)
    {
        if (!isset($this->cart[$productId])) return;

        $newQuantity = $this->cart[$productId]['jumlah'] + $change;

        if ($newQuantity <= 0) {
            unset($this->cart[$productId]);
        } else {
            $this->cart[$productId]['jumlah'] = $newQuantity;
        }
    }

    public function getSubtotalProperty()
    {
        return collect($this->cart)->sum(fn($item) => $item['harga'] * $item['jumlah']);
    }

    public function getTaxProperty()
    {
        return $this->subtotal * 0.1; // 10% pajak
    }

    public function getTotalProperty()
    {
        return $this->subtotal + $this->tax;
    }

    public function saveOrder()
    {
        // Validasi input
        // $this->validate([
        //     'nama_konsumen' => 'required|string|max:100',
        //     'no_table' => 'nullable|string|max:50'
        // ]);

        if (empty($this->cart) || $this->nama_konsumen == '') {
            session()->flash('error', 'Periksa kembali pesanan Anda. Pastikan keranjang tidak kosong dan nama konsumen diisi.');
            return;
        }

        DB::beginTransaction();
        try {
            // Buat transaksi baru
            $transaction = Transaction::create([
                'nomor_invoice' => 'INV-' . date('Ymd') . '-' . rand(1000, 9999),
                'total_pembayaran' => $this->total,
                'total_pajak' => $this->tax,
                'transaction_status' => 'unpaid',
                'payment_status' => 'pending',
                'nama_konsumen' => $this->nama_konsumen,
                'no_table' => $this->no_table
            ]);

            // Tambahkan item transaksi
            foreach ($this->cart as $productId => $item) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $productId,
                    'jumlah' => $item['jumlah'],
                    'harga' => $item['harga'],
                    'subtotal' => $item['harga'] * $item['jumlah']
                ]);
            }

            DB::commit();

            // Reset cart dan kirim notifikasi
            $this->reset(['cart', 'nama_konsumen', 'no_table']);
            session()->flash('success', 'Pesanan berhasil disimpan! Silakan tunggu konfirmasi kasir.');

            // Redirect atau refresh halaman
            return redirect()->route('customer.order.success', ['transactionId' => $transaction->id]);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal menyimpan pesanan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $categories = Kategori::all();
        $products = Product::where('status', 'Tersedia')->paginate(10);
        return view('livewire.customer-order', [
            'categories' => $categories,
            'products' => $products
        ]);
    }
}
