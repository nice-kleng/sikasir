@extends('customer.layouts')

@section('content')
    <div class="container-fluid">
        @livewire('customer-order')
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
