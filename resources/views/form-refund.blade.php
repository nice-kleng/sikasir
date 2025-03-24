@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('refund') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="amount">Jumlah Refund</label>
                            <input type="number" class="form-control" id="amount" name="amount" required>
                            {{-- <div class="form-text">Maksimal refund: Rp
                                {{ number_format($transaction->amount, 0, ',', '.') }}</div> --}}
                        </div>
                        <button type="submit" class="btn btn-success">Proses</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
