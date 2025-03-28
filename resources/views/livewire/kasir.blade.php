<div class="container-fluid">
    <div class="row">
        <!-- Main Content -->
        <div class="col-md-12 main-content p-4">
            <div class="row mb-4">
                <div class="col">
                    <h2>Transaksi Baru</h2>
                </div>
                <div class="col-auto">
                    <div class="btn-group">
                        @if (count($unpaidTransactions) > 0)
                            <button type="button" class="btn btn-warning" data-toggle="modal"
                                data-target="#unpaidOrdersModal">
                                <i class="fas fa-clock mr-2"></i>
                                Pesanan Belum Dibayar ({{ count($unpaidTransactions) }})
                            </button>
                        @endif
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-info">
                            <i class="fas fa-arrow-left mr-2"></i>Kembali
                        </a>
                        <button class="btn btn-outline-primary">
                            <i class="fas fa-user mr-2"></i>Kasir: {{ Auth::user()->name }}
                        </button>
                        <button class="btn btn-outline-secondary">
                            <i class="fas fa-clock mr-2"></i><span x-data
                                x-init="setInterval(() => $el.textContent = new Date().toLocaleTimeString(), 1000)">{{ now()->toTimeString() }}</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Products Section -->
                <div class="col-md-8">
                    <div class="input-group mb-4">
                        <input type="text" class="form-control" placeholder="Cari produk..."
                            wire:model.live="searchQuery">
                        <div class="input-group-append">
                            <button class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>

                    <div class="row">
                        @foreach ($products as $product)
                            <div class="col-md-4 mb-4">
                                <div class="card product-card">
                                    <div class="product-image-container">
                                        <img src="{{ asset('storage/' . $product->foto) }}"
                                            class="card-img-top product-image" alt="{{ $product->nama_menu }}">
                                    </div>
                                    <div class="card-body">
                                        <small
                                            class="text-{{ $product->status === 'Tersedia' ? 'success' : 'danger' }} mb-2 d-block">{{ $product->status }}</small>
                                        <h5 class="card-title">{{ $product->nama_menu }}</h5>
                                        <p class="card-text text-primary">Rp
                                            {{ number_format($product->harga, 0, ',', '.') }}</p>
                                        <button class="btn btn-primary btn-block"
                                            wire:click="addToCart({{ $product->id }})">
                                            <i class="fas fa-plus mr-2"></i>Tambah
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $products->links() }}
                    </div>
                </div>

                <!-- Cart Section -->
                <div class="col-md-4">
                    <div class="cart-section p-4">
                        <h4 class="mb-4">Keranjang Belanja</h4>
                        <div class="cart-items mb-4">
                            @foreach ($cart as $productId => $item)
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-0">{{ $item['nama_menu'] }}</h6>
                                                <small class="text-muted">Rp
                                                    {{ number_format($item['harga'], 0, ',', '.') }} x
                                                    {{ $item['jumlah'] }}</small>
                                            </div>
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-outline-primary"
                                                    wire:click="updateQuantity({{ $productId }}, -1)">-</button>
                                                <button
                                                    class="btn btn-sm btn-outline-secondary disabled">{{ $item['jumlah'] }}</button>
                                                <button class="btn btn-sm btn-outline-primary"
                                                    wire:click="updateQuantity({{ $productId }}, 1)">+</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="subtotal mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal</span>
                                <span>Rp {{ number_format($this->subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>PPN (10%)</span>
                                <span>Rp {{ number_format($this->tax, 0, ',', '.') }}</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <h5>Total</h5>
                                <h5 class="text-primary">Rp {{ number_format($this->total, 0, ',', '.') }}</h5>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nama_konsumen" class="form-label">Nama Pemesan</label>
                                        <input type="text" id="nama_konsumen" class="form-control"
                                            wire:model="nama_konsumen">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="no_table" class="form-label">Nomor Meja</label>
                                        <input type="text" id="no_table" class="form-control" wire:model="no_table">
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if (!empty($cart))
                            @if (!$paymentChoice && !$editingTransactionId)
                                <div class="payment-choice mb-4">
                                    <h5>Pilih Metode:</h5>
                                    <div class="btn-group d-flex">
                                        <button class="btn btn-outline-primary" wire:click="choosePaymentTiming('now')">
                                            Bayar Sekarang
                                        </button>
                                        <button class="btn btn-outline-secondary" wire:click="saveOrder">
                                            Bayar Nanti
                                        </button>
                                    </div>
                                </div>
                            @endif

                            @if ($editingTransactionId && !$paymentChoice)
                                <div class="d-flex justify-content-between mb-4">
                                    <button class="btn btn-secondary" wire:click="resetAll">
                                        Batal Edit
                                    </button>
                                    <button class="btn btn-primary" wire:click="saveOrder">
                                        Simpan Perubahan
                                    </button>
                                </div>
                            @endif

                            {{-- @if ($paymentChoice === 'now' && $showPaymentMethodSelection)
                                <div class="payment-methods mb-4">
                                    <div class="btn-group d-flex">
                                        <button
                                            class="btn btn-outline-primary {{ $paymentMethod === 'cash' ? 'active' : '' }}"
                                            wire:click="$set('paymentMethod', 'cash')">
                                            <i class="fas fa-money-bill-wave mr-2"></i>Tunai
                                        </button>
                                        <button
                                            class="btn btn-outline-primary {{ $paymentMethod === 'online' ? 'active' : '' }}"
                                            wire:click="$set('paymentMethod', 'online')">
                                            <i class="fas fa-globe mr-2"></i>Online
                                        </button>
                                    </div>

                                    <button class="btn btn-primary btn-lg btn-block mt-3" wire:click="preparePayment">
                                        <i class="fas fa-check-circle mr-2"></i>Proses Pembayaran
                                    </button>
                                </div>
                            @endif --}}

                            @if ($paymentChoice === 'now')
                                <!-- Input Pembayaran untuk cash -->
                                @if ($showPaymentInput && $paymentMethod === 'cash')
                                    <div class="payment-input mb-4">
                                        <div class="form-group">
                                            <label for="cashAmount">Jumlah Uang Diterima</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Rp</span>
                                                </div>
                                                <input type="number" id="cashAmount" class="form-control"
                                                    wire:model.live="cashAmount" min="{{ $this->total }}">
                                            </div>
                                        </div>

                                        <div class="change-info alert alert-info mt-3">
                                            <div class="d-flex justify-content-between">
                                                <strong>Kembalian:</strong>
                                                <strong>Rp {{ number_format($this->change, 0, ',', '.') }}</strong>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between mt-3">
                                            <button class="btn btn-outline-danger" wire:click="resetAll">
                                                <i class="fas fa-times mr-2"></i>Batal
                                            </button>
                                            <button class="btn btn-success" wire:click="processPayment"
                                                {{ $cashAmount < $this->total ? 'disabled' : '' }}>
                                                <i class="fas fa-check-circle mr-2"></i>Proses Pembayaran
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <div class="payment-methods mb-4">
                                        <div class="btn-group d-flex">
                                            <button
                                                class="btn btn-outline-primary {{ $paymentMethod === 'cash' ? 'active' : '' }}"
                                                wire:click="$set('paymentMethod', 'cash')">
                                                <i class="fas fa-money-bill-wave mr-2"></i>Tunai
                                            </button>
                                            <button
                                                class="btn btn-outline-primary {{ $paymentMethod === 'online' ? 'active' : '' }}"
                                                wire:click="$set('paymentMethod', 'online')">
                                                <i class="fas fa-globe mr-2"></i>Online
                                            </button>
                                        </div>
                                    </div>

                                    <button class="btn btn-primary btn-lg btn-block" wire:click="preparePayment">
                                        <i class="fas fa-check-circle mr-2"></i>Proses Pembayaran
                                    </button>
                                    <button class="btn btn-secondary btn-lg btn-block" wire:click="resetAll">
                                        Batal
                                    </button>
                                @endif
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Pesanan Belum Dibayar -->
    <div class="modal fade" id="unpaidOrdersModal" role="dialog" aria-labelledby="unpaidOrdersModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="unpaidOrdersModalLabel">Daftar Pesanan Belum Dibayar</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        @foreach ($unpaidTransactions as $transaction)
                            <div class="col-md-6 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h6>Invoice: {{ $transaction->nomor_invoice }}</h6>
                                        <p class="mb-1">Pemesan: {{ $transaction->nama_konsumen }}</p>
                                        <p class="mb-1">Meja: {{ $transaction->no_table }}</p>
                                        <p class="mb-2">Total: Rp
                                            {{ number_format($transaction->total_pembayaran, 0, ',', '.') }}</p>

                                        <!-- Detail Pesanan -->
                                        <div class="small text-muted mb-3">
                                            <strong>Items:</strong><br>
                                            @foreach ($transaction->items as $item)
                                                {{ $item->product->nama_menu }} ({{ $item->jumlah }}x)<br>
                                            @endforeach
                                        </div>

                                        <div class="btn-group w-100">
                                            <button class="btn btn-sm btn-primary"
                                                wire:click="editTransaction({{ $transaction->id }})"
                                                data-dismiss="modal">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button class="btn btn-sm btn-success"
                                                wire:click="startPaymentForUnpaid({{ $transaction->id }})"
                                                data-dismiss="modal">
                                                <i class="fas fa-cash-register"></i> Proses Pembayaran
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Form untuk print nota -->
    <form id="print-form" method="GET" target="_blank" class="d-none">
    </form>

    <style>
        .product-image-container {
            height: 150px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-card {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .product-card .card-body {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .product-card .btn {
            margin-top: auto;
        }
    </style>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('livewire:initialized', () => {
                // Inisialisasi modal dengan jQuery (Bootstrap 4)
                const $modal = $('#unpaidOrdersModal');

                // Event listener untuk tombol yang membuka modal
                $('[data-toggle="modal"][data-target="#unpaidOrdersModal"]').on('click', function() {
                    $modal.modal('show');
                });

                // Handle payment success (for both cash and online)
                @this.on('paymentSuccess', (data) => {
                    Swal.fire({
                        title: 'Sukses!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Automatically print nota
                        @this.call('resetPayment');
                        printNota(data[0].transactionId);
                    });
                });

                @this.on('showPaymentModal', (data) => {
                    // Simpan transaction ID untuk cetak nota nanti
                    window.currentTransactionId = data[0].transactionId;

                    // Tampilkan modal pembayaran Midtrans
                    snap.pay(data[0].snapToken, {
                        onSuccess: function(result) {
                            @this.dispatch('paymentCallback', {
                                status: 'success',
                                data: result
                            });
                            // Cetak nota setelah pembayaran berhasil
                            @this.call('resetPayment');
                            printNota(window.currentTransactionId);
                        },
                        onPending: function(result) {
                            @this.dispatch('paymentCallback', {
                                status: 'pending',
                                data: result
                            });
                        },
                        onError: function(result) {
                            @this.dispatch('paymentCallback', {
                                status: 'error',
                                data: result
                            });
                        },
                        onClose: function() {
                            @this.dispatch('paymentCallback', {
                                status: 'closed'
                            });
                        }
                    });
                });

                // Handle payment error
                @this.on('paymentError', (data) => {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        @this.call('resetPayment');
                    });
                });

                @this.on('paymentPending', result => {
                    Swal.fire({
                        title: 'Info',
                        text: 'Pembayaran dalam proses',
                        icon: 'info',
                        confirmButtonText: 'OK'
                    });
                });

                @this.on('showAlert', result => {
                    Swal.fire({
                        title: 'Peringatan',
                        text: result[0].message,
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                });

                // Auto hide modal after action
                @this.on('orderSaved', () => {
                    $modal.modal('hide');
                });

                // Update data-dismiss handler
                $modal.on('hidden.bs.modal', function() {
                    // Optional: tambahkan logika yang diperlukan setelah modal tertutup
                });
            });

            // Fungsi untuk cetak nota
            function printNota(transactionId) {
                const form = document.getElementById('print-form');
                form.action = `/kasir/print-nota/${transactionId}`;
                form.submit();
            }
        </script>
    @endpush
</div>
