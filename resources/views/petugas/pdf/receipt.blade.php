<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Struk Transaksi</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .content {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 30px;
            background-color: #fff;
            border-radius: 12px;
            max-width: 800px;
            width: 100%;
            height: auto;
        }

        h1 {
            margin-top: 0;
            text-align: center;
            color: #333;
            margin-bottom: 10px;
        }

        p {
            margin: 4px 0;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            font-size: 14px;
        }

        th, td {
            padding: 12px 16px;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f0f0f0;
            color: #333;
            text-align: left;
        }

        tr:nth-child(even) {
            background-color: #fafafa;
        }

        td {
            color: #444;
        }
    </style>
</head>
<body>
    <div class="content">

        <h1>Struk Pembelian</h1>
        <p>Member Status : {{ $transaction->customer->member_status == 'member' ? 'Member' : 'Non Member' }}</p>
        <p>No. HP : {{ $transaction->customer->member_status == 'member' ? $transaction->customer->phone_number : '-' }}</p>
        <p>Bergabung Sejak : {{ $transaction->customer->member_status == 'member' ? \Carbon\Carbon::parse($transaction->customer->joined_at)->format('d F Y') : '-' }}</p>
        <p>Point Member : {{ $transaction->customer->member_status == 'member' ? $transaction->customer->points : '-' }}</p>

        <table>
            <tr>
              <th>Nama Produk</th>
              <th>Qty</th>
              <th>Harga</th>
              <th>Subtotal</th>
            </tr>
            @php
                $totalBelanja = 0;
                foreach ($transaction->detailTransactions as $item) {
                    $totalBelanja += $item->product->price * $item->qty;
                }
            @endphp

            @foreach($transaction->detailTransactions as $detail)
                <tr>
                    <td>{{ $detail->product->product_name }}</td>
                    <td>{{ $detail->qty }}</td>
                    <td>Rp. {{ number_format($detail->product->price, 0, ',', '.') }}</td>
                    <td>Rp. {{ number_format($detail->qty * $detail->product->price, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr>
                <td rowspan="5">Point Digunakan</td>
                <td rowspan="5">{{ $transaction->point_used ?? 0 }}</td>
                <td>Total Harga</td>
                <td>Rp. {{ number_format($totalBelanja, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Harga Setelah Poin</td>
                <td>Rp. {{ number_format($transaction->discount_price, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Kembalian</td>
                <td>Rp. {{ number_format($transaction->change, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Bayar Customer</td>
                <td>Rp. {{ number_format($transaction->customer_pay, 0, ',', '.') }}</td>
            </tr>
        </table>
        <p style="text-align: center;">{{ $transaction->created_at->format('d-m-Y H:i') }} | {{ $transaction->user->name }}</p>
        <p style="text-align: center;">Terima kasih atas pembelian Anda!</p>
    </div>
</body>
</html>
