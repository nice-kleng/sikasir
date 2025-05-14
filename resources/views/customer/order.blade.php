@extends('customer.layouts')

@section('content')
    <div class="container-fluid">
        @if ($is_aktif_page)
            @livewire('customer-order')
        @else
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="card">
                        <div class="card-body text-center">
                            <h2 class="fw-bold">Maaf Layanan Tidak Tersedia.</h2>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('styles')
    <style>
        .product-card {
            transition: transform 0.3s ease;
            cursor: pointer;
        }

        .product-card:hover {
            transform: scale(1.05);
        }

        .cart-item {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }
    </style>
@endpush
