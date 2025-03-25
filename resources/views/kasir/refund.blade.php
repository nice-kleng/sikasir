@extends('layouts.app', ['title' => 'Hitory Refund'])


@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <td>No</td>
                                    <td>Nomor Invoice</td>
                                    <td>Kode Refund</td>
                                    <td>Total Refund</td>
                                    <td>Alasan Refund</td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($refunds as $refund)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $refund->transaction->nomor_invoice }}</td>
                                        <td>{{ $refund->refund_number }}</td>
                                        <td>Rp. {{ number_format($refund->total_refund, 0, ',', '.') }}</td>
                                        <td>{{ $refund->alasan }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-3">
                            {{ $refunds->appends(request()->except('page'))->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
