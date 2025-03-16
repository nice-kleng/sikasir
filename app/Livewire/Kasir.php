<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Services\MidtransService;
use Livewire\WithPagination;

class Kasir extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $cart = [];
    public $searchQuery = '';
    public $paymentMethod = 'cash';
    public $snapToken;
    public $cashAmount = 0; // Jumlah uang yang diberikan customer
    public $showPaymentInput = false; // Flag untuk menampilkan input pembayaran
    public $currentTransactionId = null; // Untuk menyimpan ID transaksi setelah proses pembayaran

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

    public function getChangeProperty()
    {
        return max(0, $this->cashAmount - $this->total);
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

    public function preparePayment()
    {
        if (empty($this->cart)) {
            $this->dispatch('showAlert', [
                'type' => 'error',
                'message' => 'Pilih minimal 1 menu terlebih dahulu!'
            ]);
            return;
        }

        if ($this->paymentMethod === 'cash') {
            $this->showPaymentInput = true;
        } else {
            $this->processPayment();
        }
    }

    public function processPayment()
    {
        if (empty($this->cart)) {
            $this->dispatch('showAlert', [
                'type' => 'error',
                'message' => 'Pilih minimal 1 menu terlebih dahulu!'
            ]);
            return;
        }

        // Validasi untuk pembayaran cash
        if ($this->paymentMethod === 'cash' && $this->cashAmount < $this->total) {
            $this->dispatch('showAlert', [
                'type' => 'error',
                'message' => 'Jumlah uang yang diberikan kurang dari total pembayaran!'
            ]);
            return;
        }

        $invoiceNumber = 'INV-' . date('Ymd') . '-' . rand(1000, 9999);

        $transaction = Transaction::create([
            'nomor_invoice' => $invoiceNumber,
            'total_pembayaran' => $this->total,
            'total_pajak' => $this->tax,
            'payment_method' => $this->paymentMethod,
            'user_id' => auth()->id(),
            'payment_status' => $this->paymentMethod === 'cash' ? 'paid' : 'pending',
            'cash_amount' => $this->paymentMethod === 'cash' ? $this->cashAmount : null,
            'cash_change' => $this->paymentMethod === 'cash' ? $this->change : null
        ]);

        $this->currentTransactionId = $transaction->id;

        foreach ($this->cart as $productId => $item) {
            TransactionItem::create([
                'transaction_id' => $transaction->id,
                'product_id' => $productId,
                'jumlah' => $item['jumlah'],
                'harga' => $item['harga'],
                'subtotal' => $item['harga'] * $item['jumlah']
            ]);
        }

        if ($this->paymentMethod === 'cash') {
            $this->resetPayment();
            $this->dispatch('paymentSuccess', [
                'message' => 'Pembayaran tunai berhasil!',
                'transactionId' => $transaction->id
            ]);
            return;
        }

        // Proses pembayaran online melalui Midtrans
        $midtransService = new MidtransService();
        $snapToken = $midtransService->createTransaction($transaction);

        if ($snapToken) {
            $transaction->update(['snap_token' => $snapToken]);
            $this->snapToken = $snapToken;
            $this->dispatch('showPaymentModal', [
                'snapToken' => $snapToken,
                'transactionId' => $transaction->id
            ]);
        } else {
            $this->dispatch('paymentError', ['message' => 'Gagal memproses pembayaran']);
        }
    }

    public function resetPayment()
    {
        $this->cart = [];
        $this->cashAmount = 0;
        $this->showPaymentInput = false;
        $this->currentTransactionId = null;
    }

    public function cancelCashPayment()
    {
        $this->showPaymentInput = false;
        $this->cashAmount = 0;
    }

    public function render()
    {
        $products = Product::when($this->searchQuery, function ($query) {
            $query->where('nama_menu', 'like', '%' . $this->searchQuery . '%');
        })->paginate(6);

        return view('livewire.kasir', [
            'products' => $products
        ]);
    }
}
