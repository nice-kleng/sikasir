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
                                        <img src="{{ asset('images/' . $product->foto) }}"
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
                            <div class="d-flex justify-content-between">
                                <h5>Total</h5>
                                <h5 class="text-primary">Rp {{ number_format($this->total, 0, ',', '.') }}</h5>
                            </div>
                        </div>

                        <!-- Payment Input Section (for cash) -->
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
                                    <button class="btn btn-outline-danger" wire:click="cancelCashPayment">
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
                        @endif
                    </div>
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
                // Handle payment success (for both cash and online)
                @this.on('paymentSuccess', (data) => {
                    Swal.fire({
                        title: 'Sukses!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Automatically print nota
                        printNota(data[0].transactionId);
                    });
                });

                // Handle payment modal for online payment
                // @this.on('showPaymentModal', (data) => {
                //     // Simpan transaction ID untuk cetak nota nanti
                //     window.currentTransactionId = data.transactionId;

                //     // Tampilkan modal pembayaran Midtrans
                //     snap.pay(data.snapToken, {
                //         onSuccess: function(result) {
                //             @this.dispatch('paymentCallback', {
                //                 status: 'success',
                //                 data: result
                //             });
                //             // Cetak nota setelah pembayaran berhasil
                //             printNota(window.currentTransactionId);
                //         },
                //         onPending: function(result) {
                //             @this.dispatch('paymentCallback', {
                //                 status: 'pending',
                //                 data: result
                //             });
                //         },
                //         onError: function(result) {
                //             @this.dispatch('paymentCallback', {
                //                 status: 'error',
                //                 data: result
                //             });
                //         },
                //         onClose: function() {
                //             @this.dispatch('paymentCallback', {
                //                 status: 'closed'
                //             });
                //         }
                //     });
                // });

                // Handle payment error
                @this.on('paymentError', (data) => {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
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
