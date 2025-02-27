<!DOCTYPE html>
<html>

<head>
    <title>Laporan Transaksi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>
    <h2>Laporan Transaksi</h2>

    <table>
        <thead>
            <tr>
                <th>No. Invoice</th>
                <th>Tanggal</th>
                <th>Metode Pembayaran</th>
                <th>Status</th>
                <th>Total Pembayaran</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->nomor_invoice }}</td>
                    <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $transaction->payment_method }}</td>
                    <td>{{ $transaction->payment_status }}</td>
                    <td class="text-right">Rp {{ number_format($transaction->total_pembayaran, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="3"><strong>Total</strong></td>
                <td colspan="2" class="text-right"><strong>Rp
                        {{ number_format($transactions->sum('total_pembayaran'), 0, ',', '.') }}</strong></td>
            </tr>
        </tbody>
    </table>
</body>

</html>
