<?php

namespace App\Http\Controllers;

use App\Models\Refund;
use App\Models\Transaction;
use Illuminate\Http\Request;

class RefundController extends Controller
{
    public function index()
    {
        $refunds = Refund::with('transaction')->paginate(10);
        return view('kasir.refund', compact('refunds'));
    }

    public function create($transaction_id)
    {
        $transaction = Transaction::find($transaction_id);
        return view('kasir.form-refund', compact('transaction'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'alasan' => 'required',
            'total_refund' => 'required|numeric'
        ]);

        $transaction = Transaction::find($request->transaction_id);
        if ($request->total_refund > $transaction->total_pembayaran) {
            return redirect()->back()->with('error', 'Total refund harus melebihi total pembayaran');
        }

        $refund_number = 'RF' . date('ymdHis');

        $refund = new Refund();
        $refund->transaction_id = $request->transaction_id;
        $refund->refund_number = $refund_number;
        $refund->alasan = $request->alasan;
        $refund->total_refund = $request->total_refund;
        $refund->refund_status = 'success';
        $refund->save();

        $transaction->total_pembayaran -= $request->total_refund;
        $transaction->transaction_status = 'refund';
        $transaction->payment_status = 'refund';
        $transaction->save();

        return redirect()->route('report.index')->with('success', 'Refund berhasil diproses');
    }
}
