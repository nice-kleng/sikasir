<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota - {{ $transaction->nomor_invoice }}</title>
    <style>
        body {
            font-family: 'Arial Narrow', Arial, sans-serif;
            font-size: 8pt;
            line-height: 1.1;
            color: #000;
            margin: 0;
            padding: 0;
            width: 100%;
        }

        .container {
            width: 65mm;
            margin: 0 auto;
            box-sizing: border-box;
        }

        .header {
            text-align: center;
            margin-bottom: 5px;
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
        }

        .header h1 {
            font-size: 10pt;
            margin: 0 0 2px;
        }

        .header p {
            font-size: 7pt;
            margin: 0 0 1px;
        }

        .info {
            margin-bottom: 5px;
            font-size: 7pt;
        }

        .info-item {
            margin-bottom: 1px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
            font-size: 7pt;
        }

        th,
        td {
            padding: 1px;
            text-align: left;
        }

        .item-name {
            width: 95%;
            font-size: 7pt;
        }

        .item-detail {
            padding-left: 5px;
        }

        .item-amount {
            text-align: right;
            width: 40px;
        }

        .total-section {
            margin-top: 3px;
            text-align: right;
            font-size: 7pt;
        }

        .total-row {
            margin-bottom: 1px;
            clear: both;
            width: 100%;
        }

        .total-label {
            display: inline-block;
            width: 45%;
            text-align: right;
            padding-right: 5px;
        }

        .total-value {
            display: inline-block;
            width: 40%;
            text-align: right;
        }

        .grand-total {
            font-weight: bold;
            font-size: 8pt;
            border-top: 1px solid #000;
            padding-top: 2px;
            margin-top: 2px;
        }

        .footer {
            margin-top: 5px;
            text-align: center;
            font-size: 7pt;
            border-top: 1px dashed #000;
            padding-top: 3px;
        }

        .footer p {
            margin: 0 0 1px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name', 'Kasir') }}</h1>
            <p>Jl. Contoh No. 123</p>
            <p>Telp: (021) 1234-5678</p>
        </div>

        <div class="info">
            <div class="info-item">#{{ $transaction->nomor_invoice }}</div>
            <div class="info-item">{{ $transaction->created_at->format('d/m/Y H:i') }}</div>
            <div class="info-item">{{ $transaction->user->name }} |
                {{ $transaction->payment_method === 'cash' ? 'Tunai' : 'Online' }}</div>
        </div>

        <table>
            <tbody>
                @foreach ($transaction->items as $item)
                    <tr>
                        <td class="item-name" colspan="2">{{ $item->product->nama_menu }}</td>
                    </tr>
                    <tr>
                        <td class="item-detail">{{ $item->jumlah }}x{{ number_format($item->harga, 0, ',', '.') }}</td>
                        <td class="item-amount">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-section">
            <div class="total-row">
                <span class="total-label">Subtotal</span>
                <span
                    class="total-value">{{ number_format($transaction->total_pembayaran - $transaction->total_pajak, 0, ',', '.') }}</span>
            </div>
            <div class="total-row">
                <span class="total-label">PPN</span>
                <span class="total-value">{{ number_format($transaction->total_pajak, 0, ',', '.') }}</span>
            </div>
            <div class="total-row grand-total">
                <span class="total-label">TOTAL</span>
                <span class="total-value">{{ number_format($transaction->total_pembayaran, 0, ',', '.') }}</span>
            </div>

            @if ($transaction->payment_method === 'cash')
                <div class="total-row">
                    <span class="total-label">Tunai</span>
                    <span class="total-value">{{ number_format($transaction->cash_amount, 0, ',', '.') }}</span>
                </div>
                <div class="total-row">
                    <span class="total-label">Kembali</span>
                    <span class="total-value">{{ number_format($transaction->cash_change, 0, ',', '.') }}</span>
                </div>
            @endif
        </div>

        <div class="footer">
            <p>Terima kasih atas kunjungan Anda</p>
        </div>
    </div>
</body>

</html>
