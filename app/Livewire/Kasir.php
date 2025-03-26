<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Services\MidtransService;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

class Kasir extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $cart = [];
    public $searchQuery = '';
    public $paymentMethod = 'cash';
    public $snapToken;
    public $nama_konsumen;
    public $no_table;
    public $cashAmount = 0; // Jumlah uang yang diberikan customer
    public $showPaymentInput = false; // Flag untuk menampilkan input pembayaran
    public $currentTransactionId = null; // Untuk menyimpan ID transaksi setelah proses pembayaran
    public $paymentChoice = null; // 'now' or 'later'
    public $editingTransactionId = null;
    public $showPaymentMethodSelection = false;
    public $unpaidTransactions = []; // To store unpaid transactions

    public function mount()
    {
        $this->loadUnpaidTransactions();
    }

    public function loadUnpaidTransactions()
    {
        $this->unpaidTransactions = Transaction::where('transaction_status', 'unpaid')
            ->with('items.product')
            ->get();
    }

    public function editTransaction($transactionId)
    {
        $transaction = Transaction::with('items')->find($transactionId);
        $this->editingTransactionId = $transactionId;
        $this->cart = [];

        foreach ($transaction->items as $item) {
            $this->cart[$item->product_id] = [
                'nama_menu' => $item->product->nama_menu,
                'harga' => $item->harga,
                'jumlah' => $item->jumlah
            ];
        }

        $this->nama_konsumen = $transaction->nama_konsumen;
        $this->no_table = $transaction->no_table;
    }

    public function choosePaymentTiming($choice)
    {
        $this->paymentChoice = $choice;
        if ($choice === 'now') {
            $this->showPaymentMethodSelection = true;
        }
    }

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
            $this->showPaymentMethodSelection = false;
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

        // Jika sedang mengedit transaksi yang ada
        if ($this->editingTransactionId) {
            $transaction = Transaction::find($this->editingTransactionId);
        } else {
            $transaction = new Transaction();
            $transaction->nomor_invoice = 'INV-' . date('Ymd') . '-' . rand(1000, 9999);
        }

        DB::beginTransaction();
        $transaction->fill([
            'total_pembayaran' => $this->total,
            'total_pajak' => $this->tax,
            'payment_method' => $this->paymentMethod,
            'user_id' => auth()->id(),
            'payment_status' => $this->paymentMethod === 'cash' ? 'paid' : 'pending',
            'transaction_status' => 'paid',
            'cash_amount' => $this->paymentMethod === 'cash' ? $this->cashAmount : null,
            'cash_change' => $this->paymentMethod === 'cash' ? $this->change : null,
            'nama_konsumen' => $this->nama_konsumen,
            'no_table' => $this->no_table,
        ]);
        $transaction->save();

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
            DB::commit();
            $this->resetPayment();
            $this->dispatch('paymentSuccess', [
                'message' => 'Pembayaran tunai berhasil!',
                'transactionId' => $transaction->id
            ]);
            return;
        }

        $midtransService = new MidtransService();
        $snapToken = $midtransService->createTransaction($transaction);

        if ($snapToken) {
            DB::commit();
            $transaction->update(['snap_token' => $snapToken]);
            $this->snapToken = $snapToken;
            $this->dispatch('showPaymentModal', [
                'snapToken' => $snapToken,
                'transactionId' => $transaction->id
            ]);
        } else {
            DB::rollBack();
            $this->resetPayment();
            $this->dispatch('paymentError', ['message' => 'Gagal memproses pembayaran']);
        }
    }

    public function saveOrder()
    {
        if (empty($this->cart)) {
            $this->dispatch('showAlert', [
                'type' => 'error',
                'message' => 'Pilih minimal 1 menu terlebih dahulu!'
            ]);
            return;
        }

        $transaction = $this->editingTransactionId
            ? Transaction::find($this->editingTransactionId)
            : new Transaction();

        $transaction->fill([
            'nomor_invoice' => $this->editingTransactionId ? $transaction->nomor_invoice : 'INV-' . date('Ymd') . '-' . rand(1000, 9999),
            'total_pembayaran' => $this->total,
            'total_pajak' => $this->tax,
            'user_id' => auth()->id(),
            'transaction_status' => 'unpaid',
            'payment_status' => 'pending',
            'nama_konsumen' => $this->nama_konsumen,
            'no_table' => $this->no_table,
        ]);
        $transaction->save();

        // Delete existing items if editing
        if ($this->editingTransactionId) {
            $transaction->items()->delete();
        }

        foreach ($this->cart as $productId => $item) {
            TransactionItem::create([
                'transaction_id' => $transaction->id,
                'product_id' => $productId,
                'jumlah' => $item['jumlah'],
                'harga' => $item['harga'],
                'subtotal' => $item['harga'] * $item['jumlah']
            ]);
        }

        $this->resetAll();
        $this->loadUnpaidTransactions();
        $this->dispatch('orderSaved', ['message' => 'Pesanan berhasil disimpan!']);
    }

    public function resetAll()
    {
        $this->reset([
            'cart',
            'paymentChoice',
            'editingTransactionId',
            'nama_konsumen',
            'no_table',
            'showPaymentInput',
            'paymentMethod',
            'showPaymentMethodSelection',
            'cashAmount'
        ]);
        $this->loadUnpaidTransactions(); // Reload unpaid transactions
    }

    public function resetPayment()
    {
        $this->reset([
            'cart',
            'cashAmount',
            'showPaymentInput',
            'currentTransactionId',
            'nama_konsumen',
            'no_table',
            'paymentChoice',
            'editingTransactionId',
            'showPaymentMethodSelection'
        ]);
        $this->loadUnpaidTransactions(); // Reload unpaid transactions
    }

    public function cancelCashPayment()
    {
        $this->showPaymentInput = false;
        $this->cashAmount = 0;
        $this->showPaymentMethodSelection = false;
    }

    public function startPaymentForUnpaid($transactionId)
    {
        $transaction = Transaction::find($transactionId);
        $this->editingTransactionId = $transactionId;
        $this->cart = [];

        // Load transaction items to cart
        foreach ($transaction->items as $item) {
            $this->cart[$item->product_id] = [
                'nama_menu' => $item->product->nama_menu,
                'harga' => $item->harga,
                'jumlah' => $item->jumlah
            ];
        }

        $this->nama_konsumen = $transaction->nama_konsumen;
        $this->no_table = $transaction->no_table;
        $this->paymentChoice = 'now';
    }

    public function render()
    {
        $products = Product::when($this->searchQuery, function ($query) {
            $query->where('nama_menu', 'like', '%' . $this->searchQuery . '%');
        })->paginate(6);

        return view('livewire.kasir', [
            'products' => $products,
            'unpaidTransactions' => Transaction::where('transaction_status', 'unpaid')->get()
        ]);
    }
}
