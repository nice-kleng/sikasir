<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['items.product'])->orderBy('created_at', 'desc');

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }

        $transactions = $query->paginate(10);

        return view('report', compact('transactions'));
    }

    public function showItem(string $id)
    {
        $transactions = Transaction::with(['items.product'])->find($id);
        return response()->json($transactions);
    }

    public function generatePDF(Request $request)
    {
        $query = Transaction::with(['items.product'])->orderBy('created_at', 'desc');

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }

        $transactions = $query->get();

        $pdf = Pdf::loadView('reports.pdf', compact('transactions'));

        return $pdf->stream('transaction-report.pdf');
    }


    public function laporanPenjualan(Request $request)
    {
        $transactions = TransactionItem::with(['product'])
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->selectRaw('MIN(transactions.created_at) as min_created_at, MAX(transactions.created_at) as max_created_at, SUM(transaction_items.jumlah * transaction_items.harga) as total_pembayaran, transaction_items.product_id, sum(transaction_items.jumlah) as total_terjual, products.nama_menu as product_name, products.id as product_id')
            ->where('transactions.payment_status', 'paid')
            ->groupBy('transaction_items.product_id', 'products.nama_menu', 'products.id');

        if ($request->start_date && $request->end_date) {
            $transactions->whereBetween('transactions.created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }


        $transactions->orderBy('total_pembayaran', 'desc');
        return view('laporan-penjualan', [
            'transactions' => $transactions->get(),
        ]);
    }
}
