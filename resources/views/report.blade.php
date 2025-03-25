@extends('layouts.app', ['title' => 'Laporan Transaksi'])

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Laporan Transaksi</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('report.index') }}" method="GET" class="mb-4">
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
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No. Invoice</th>
                                    <th>Pembeli</th>
                                    <th>Tanggal</th>
                                    <th>Total Pembayaran</th>
                                    <th>Metode Pembayaran</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->nomor_invoice }}</td>
                                        <td>{{ $transaction->nama_konsumen }}</td>
                                        <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                                        <td>Rp {{ number_format($transaction->total_pembayaran, 0, ',', '.') }}</td>
                                        <td>{{ $transaction->payment_method }}</td>
                                        <td>{{ $transaction->payment_status }}</td>
                                        <td>
                                            <a href="{{ route('refund.create', $transaction->id) }}"
                                                class="btn btn-sm btn-warning">Refund</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-3">
                            {{ $transactions->appends(request()->except('page'))->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
