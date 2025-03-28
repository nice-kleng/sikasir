@extends('customer.layouts')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h4 class="text-center">Pesanan Berhasil</h4>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                        </div>
                        <h5 class="text-center mb-3">Terima Kasih!</h5>
                        <p class="text-center">
                            Pesanan Anda telah diterima dengan nomor invoice
                            <strong>{{ $transaction->nomor_invoice }}</strong>
                        </p>
                        <div class="alert alert-info">
                            <strong>Ringkasan Pesanan:</strong><br>
                            Nama: {{ $transaction->nama_konsumen }}<br>
                            Meja: {{ $transaction->no_table ?? 'Tidak Ditentukan' }}<br>
                            Total: Rp {{ number_format($transaction->total_pembayaran, 0, ',', '.') }}
                        </div>
                        <div class="text-center">
                            <p class="text-muted">
                                Silakan tunggu konfirmasi dari kasir.
                                Pesanan Anda akan segera diproses.
                            </p>
                            <a href="{{ route('customer.order') }}" class="btn btn-primary">
                                Kembali ke Pemesanan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
