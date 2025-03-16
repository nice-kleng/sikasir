<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use PDF;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['items.product'])->orderBy('created_at', 'desc');

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }

        $transactions = $query->paginate(10)->withQueryString();

        return view('report', compact('transactions'));
    }

    public function generatePDF(Request $request)
    {
        $query = Transaction::with(['items.product'])->orderBy('created_at', 'desc');

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }

        $transactions = $query->get();

        $pdf = PDF::loadView('reports.pdf', compact('transactions'));

        return $pdf->download('transaction-report.pdf');
    }
}
