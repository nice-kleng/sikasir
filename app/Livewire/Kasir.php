<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Services\MidtransService;

class Kasir extends Component
{
    public $cart = [];
    public $searchQuery = '';
    public $paymentMethod = 'cash';
    public $snapToken;

    // Computed properties
    public function getSubtotalProperty()
    {
        return collect($this->cart)->sum(fn($item) => $item['harga'] * $item['jumlah']);
    }

    public function getTaxProperty()
    {
        return $this->subtotal * 0.1; // 10% tax
    }

    public function getTotalProperty()
    {
        return $this->subtotal + $this->tax;
    }

    // Methods
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

    public function processPayment()
    {
        $transaction = Transaction::create([
            'nomor_invoice' => 'INV-' . time(),
            'total_pembayaran' => $this->total,
            'total_pajak' => $this->tax,
            'payment_method' => $this->paymentMethod,
            'user_id' => auth()->id(),
            'payment_status' => 'pending'
        ]);

        foreach ($this->cart as $productId => $item) {
            TransactionItem::create([
                'transaction_id' => $transaction->id,
                'product_id' => $productId,
                'jumlah' => $item['jumlah'],
                'harga' => $item['harga'],
                'subtotal' => $item['harga'] * $item['jumlah']
            ]);
        }

        // $this->cart = [];
        // $this->dispatch('paymentSuccess');
        // Generate Snap Token
        $midtransService = new MidtransService();
        $snapToken = $midtransService->createTransaction($transaction);
        // dd($snapToken);

        if ($snapToken) {
            $transaction->update(['snap_token' => $snapToken]);
            $this->snapToken = $snapToken;
            $this->dispatch('showPaymentModal', ['snapToken' => $snapToken]);
        } else {
            $this->dispatch('paymentError', ['message' => 'Gagal memproses pembayaran']);
        }
    }

    public function render()
    {
        $products = Product::when($this->searchQuery, function ($query) {
            $query->where('nama_menu', 'like', '%' . $this->searchQuery . '%');
        })->get();

        return view('livewire.kasir', [
            'products' => $products
        ]);
    }
}
