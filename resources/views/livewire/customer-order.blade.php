<div>
    @if (session()->has('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        <!-- Kategori dan Produk -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header text-center" style="background-color: #6F4E37; color: white;">
                    <h4 class="mb-0">Menu Categories</h4>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                        @foreach ($categories as $category)
                            <button wire:click="$dispatch('filterByCategory', { categoryId: {{ $category->id }} })"
                                class="btn btn-outline-primary px-4 py-2">
                                {{ $category->nama_kategori }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="row g-4" id="product-list">
                @foreach ($products as $product)
                    <div class="col-md-4">
                        <div class="card h-100 product-card" wire:click="addToCart({{ $product->id }})"
                            style="cursor: pointer; overflow: hidden;">
                            <div class="position-relative">
                                <img src="{{ asset('storage/' . $product->foto) }}" class="card-img-top"
                                    style="height: 200px; object-fit: cover; transition: transform 0.3s ease;"
                                    alt="{{ $product->nama_menu }}">
                                <div class="position-absolute top-0 end-0 m-2 p-2 badge bg-primary">
                                    Rp {{ number_format($product->harga, 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="card-body text-center">
                                <h5 class="card-title mb-2">{{ $product->nama_menu }}</h5>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Add Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $products->links() }}
            </div>
        </div>

        <!-- Keranjang -->
        <div class="col-md-4">
            <div class="card sticky-top" style="top: 20px">
                <div class="card-header text-center" style="background-color: #6F4E37; color: white;">
                    <h4 class="mb-0">Your Order</h4>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    @if (empty($cart))
                        <div class="text-center py-4">
                            <i class="fas fa-shopping-cart mb-3" style="font-size: 3rem; color: #6F4E37;"></i>
                            <p class="text-muted">Your cart is empty</p>
                        </div>
                    @else
                        @foreach ($cart as $productId => $item)
                            <div class="cart-item mb-3 p-2 border-bottom">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0">{{ $item['nama_menu'] }}</h6>
                                        <small class="text-primary">Rp
                                            {{ number_format($item['harga'], 0, ',', '.') }}</small>
                                    </div>
                                    <div class="btn-group">
                                        <button wire:click="updateQuantity({{ $productId }}, -1)"
                                            class="btn btn-sm btn-outline-primary">−</button>
                                        <span class="btn btn-sm btn-outline-primary disabled">
                                            {{ $item['jumlah'] }}
                                        </span>
                                        <button wire:click="updateQuantity({{ $productId }}, 1)"
                                            class="btn btn-sm btn-outline-primary">+</button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
                <div class="card-footer bg-white">
                    <div class="mb-3">
                        <label for="nama_konsumen" class="form-label">Nama Pemesan</label>
                        <input type="text" wire:model="nama_konsumen" class="form-control"
                            placeholder="Masukkan nama Anda" required>
                    </div>
                    <div class="mb-3">
                        <label for="no_table" class="form-label">Nomor Meja (Opsional)</label>
                        <input type="text" wire:model="no_table" class="form-control" placeholder="Nomor Meja">
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <span>Rp {{ number_format($this->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>PPN (10%)</span>
                        <span>Rp {{ number_format($this->tax, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <h5>Total</h5>
                        <h5 class="text-primary">Rp {{ number_format($this->total, 0, ',', '.') }}</h5>
                    </div>
                    <button wire:click="saveOrder"
                        class="btn btn-primary btn-lg w-100 position-relative overflow-hidden">
                        <span class="d-block py-2">Place Order</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <style>
        .product-card:hover img {
            transform: scale(1.05);
        }

        .cart-item {
            transition: background-color 0.3s ease;
        }

        .cart-item:hover {
            background-color: #f8f9fa;
        }

        .btn-group .btn {
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #6F4E37;
            box-shadow: 0 0 0 0.2rem rgba(111, 78, 55, 0.25);
        }
    </style>
@endpush

@push('scripts')
    <script src="https://kit.fontawesome.com/your-code.js" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            // Tambahkan event listener untuk filter kategori
            Livewire.on('filterByCategory', (event) => {
                fetch(`/customer/products/${event.categoryId}`)
                    .then(response => response.json())
                    .then(data => {
                        const productList = document.getElementById('product-list');
                        productList.innerHTML = data.map(product => `
                            <div class="col-md-4">
                                <div class="card h-100 product-card" wire:click="addToCart(${product.id})"
                                    style="cursor: pointer; overflow: hidden;">
                                    <div class="position-relative">
                                        <img src="/storage/${product.foto}" class="card-img-top"
                                            style="height: 200px; object-fit: cover; transition: transform 0.3s ease;"
                                            alt="${product.nama_menu}">
                                        <div class="position-absolute top-0 end-0 m-2 p-2 badge bg-primary">
                                            Rp ${product.harga.toLocaleString('id-ID')}
                                        </div>
                                    </div>
                                    <div class="card-body text-center">
                                        <h5 class="card-title mb-2">${product.nama_menu}</h5>
                                    </div>
                                </div>
                            </div>
                        `).join('');

                        // Update pagination if provided
                        if (data.links) {
                            const paginationContainer = document.querySelector('.pagination-container');
                            if (paginationContainer) {
                                paginationContainer.innerHTML = data.links;
                            }
                        }
                    });
            });
        });
    </script>
@endpush
