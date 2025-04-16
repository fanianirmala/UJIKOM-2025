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
        <p>Member Status : Member</p>
        <p>No. HP : 111</p>
        <p>Bergabung Sejak : 10-04-25</p>
        <p>Point Member : 67898</p>

        <table>
            <tr>
              <th>Nama Produk</th>
              <th>Qty</th>
              <th>Harga</th>
              <th>Subtotal</th>
            </tr>
                <tr>
                    <td>Kucing Lucu</td>
                    <td>12</td>
                    <td>Rp. 80.000</td>
                    <td>Rp. 240.000</td>
                </tr>
            <tr>
                <td rowspan="4">Point Digunakan</td>
                <td rowspan="4">17013</td>
                <td>Total Harga</td>
                <td>Rp. 240.000</td>
            </tr>
            <tr>
                <td>Harga Setelah Poin</td>
                <td>Rp. 235.680</td>
            </tr>
            <tr>
                <td>Total Kembalian</td>
                <td>Rp. 10.328</td>
            </tr>
        </table>
        <p style="text-align: center;">10-04-25 | Petugas</p>
        <p style="text-align: center;">Terima kasih atas pembelian Anda!</p>
    </div>
</body>
</html>
