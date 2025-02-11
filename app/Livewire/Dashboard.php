<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Transaction;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public $periode = 'hari';

    public function getPendapatanHariIni()
    {
        $query = Transaction::where('payment_status', 'paid');

        switch ($this->periode) {
            case 'minggu':
                $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'bulan':
                $query->whereMonth('created_at', Carbon::now()->month);
                break;
            case 'tahun':
                $query->whereYear('created_at', Carbon::now()->year);
                break;
            default:
                $query->whereDate('created_at', Carbon::today());
        }

        return $query->sum('total_pembayaran');
    }

    public function getTotalTransaksiHariIni()
    {
        $query = Transaction::where('payment_status', 'paid');

        switch ($this->periode) {
            case 'minggu':
                $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'bulan':
                $query->whereMonth('created_at', Carbon::now()->month);
                break;
            case 'tahun':
                $query->whereYear('created_at', Carbon::now()->year);
                break;
            default:
                $query->whereDate('created_at', Carbon::today());
        }

        return $query->count();
    }

    public function getTopProducts()
    {
        $query = DB::table('transaction_items')
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->select('products.nama_menu', DB::raw('SUM(transaction_items.jumlah) as total_terjual'))
            ->where('transactions.payment_status', 'paid');

        switch ($this->periode) {
            case 'minggu':
                $query->whereBetween('transactions.created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'bulan':
                $query->whereMonth('transactions.created_at', Carbon::now()->month);
                break;
            case 'tahun':
                $query->whereYear('transactions.created_at', Carbon::now()->year);
                break;
            default:
                $query->whereDate('transactions.created_at', Carbon::today());
        }

        return $query->groupBy('products.id', 'products.nama_menu')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get();
    }

    public function getChartData()
    {
        // Query untuk selalu menampilkan data bulanan dalam setahun, terlepas dari periode yang dipilih
        $query = Transaction::where('payment_status', 'paid')
            ->whereYear('created_at', Carbon::now()->year)
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('MONTHNAME(created_at) as month_name'),
                DB::raw('SUM(total_pembayaran) as total')
            )
            ->groupBy('month', 'month_name')
            ->orderBy('month');

        $results = $query->get();

        // Siapkan data untuk 12 bulan
        $monthlyData = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthName = Carbon::create(null, $month)->format('F');
            $found = $results->firstWhere('month', $month);
            $monthlyData[] = [
                'date' => $monthName,
                'total' => $found ? $found->total : 0
            ];
        }

        return $monthlyData;
    }

    public function render()
    {
        return view('livewire.dashboard', [
            'pendapatan_hari_ini' => $this->getPendapatanHariIni(),
            'total_transaksi' => $this->getTotalTransaksiHariIni(),
            'top_products' => $this->getTopProducts(),
            'chart_data' => $this->getChartData(),
        ]);
    }
}
