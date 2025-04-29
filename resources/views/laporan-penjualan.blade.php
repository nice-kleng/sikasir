@extends('layouts.app', ['title' => 'Laporan Penjualan'])

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    @if (request('start_date') && request('end_date'))
                        <h4>Total Pendapatan dari {{ request('start_date') }} sampai {{ request('end_date') }} :
                            {{ $transactions->sum('total_pembayaran') }}</h4>
                    @else
                        <h4>Total Pendapatan dari
                            {{ \Carbon\Carbon::parse($transactions->min('min_created_at'))->format('d-m-Y') }} sampai
                            {{ \Carbon\Carbon::parse($transactions->max('max_created_at'))->format('d-m-Y') }} :
                            Rp {{ number_format($transactions->sum('total_pembayaran'), 0, ',', '.') }}</h4>
                    @endif
                </div>
                <div class="card-body">
                    <form action="" method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Tanggal Awal</label>
                                    <input type="date" name="start_date" class="form-control"
                                        value="{{ request('start_date') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Tanggal Akhir</label>
                                    <input type="date" name="end_date" class="form-control"
                                        value="{{ request('end_date') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                        <a href="{{ route('report.pdf') }}?start_date={{ request('start_date') }}&end_date={{ request('end_date') }}"
                                            class="btn btn-secondary" target="_blank">
                                            Export PDF
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered text-nowratp">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Menu</th>
                                    <th>Subtotal</th>
                                    <th>Total Terjual</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transactions as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->product_name }}</td>
                                        <td>Rp {{ number_format($item->total_pembayaran, 0, ',', '.') }}</td>
                                        <td>{{ $item->total_terjual }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
