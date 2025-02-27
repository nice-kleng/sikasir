<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2>Dashboard</h2>
        </div>
        @if (auth()->user()->role == 'admin')
            <div class="col-auto">
                <select wire:model.live="periode" class="form-control">
                    <option value="hari">Hari Ini</option>
                    <option value="minggu">Minggu Ini</option>
                    <option value="bulan">Bulan Ini</option>
                    <option value="tahun">Tahun Ini</option>
                </select>
            </div>
        @endif
    </div>

    <!-- Statistik Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Pendapatan Hari Ini</h5>s
                    <h3 class="mb-0">Rp {{ number_format($pendapatan_hari_ini, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Transaksi</h5>
                    <h3 class="mb-0">{{ $total_transaksi }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Rata-rata Transaksi</h5>
                    <h3 class="mb-0">Rp
                        {{ $total_transaksi > 0 ? number_format($pendapatan_hari_ini / $total_transaksi, 0, ',', '.') : 0 }}
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Grafik Penjualan -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Grafik Penjualan Bulanan {{ date('Y') }}</h5>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" wire:ignore></canvas>
                </div>
            </div>
        </div>

        <!-- Top Produk -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Produk Terlaris</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @foreach ($top_products as $product)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $product->nama_menu }}
                                <span class="badge badge-primary badge-pill">{{ $product->total_terjual }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        let salesChart;

        // Hanya inisialisasi chart saat pertama kali load
        document.addEventListener('livewire:initialized', function() {
            initChart();
        });

        // Hapus event listener yang tidak diperlukan
        function initChart() {
            const chartData = @json($chart_data);
            const ctx = document.getElementById('salesChart').getContext('2d');

            salesChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.map(item => item.date),
                    datasets: [{
                        label: 'Penjualan Bulanan',
                        data: chartData.map(item => item.total),
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgb(75, 192, 192)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                                }
                            }
                        }
                    }
                }
            });
        }
    </script>
@endpush
