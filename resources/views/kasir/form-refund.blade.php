@extends('layouts.app', ['title' => 'Proses Refund ' . $transaction->nomor_invoice])

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('refund.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="total_refund">Jumlah Refund</label>
                            <input type="hidden" name="transaction_id" value="{{ $transaction->id }}">
                            <input type="number" class="form-control" id="total_refund" name="total_refund"
                                max="{{ $transaction->total_pembayaran }}" required>
                            <div class="form-text">Maksimal refund: Rp
                                {{ number_format($transaction->total_pembayaran, 0, ',', '.') }}</div>
                        </div>
                        <div class="form-group">
                            <label for="alasan">alasan</label>
                            <textarea class="form-control" id="alasan" name="alasan" required></textarea>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('report.index') }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-success">Proses</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
