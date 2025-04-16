@extends('template.sidebar')
@section('content')
<div class="card p-4" style="width: 1215px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);">
    <div class="d-flex justify-content-between">
        <div class="d-flex" style="gap: 5px;">
            <a href="{{ route('petugas.pembelian') }}" class="btn btn-secondary" style="height: 40px;">Kembali</a>
            <a href="#" class="btn btn-primary" style="height: 40px;">Unduh</a>
        </div>
        <div class="info text-end mt-2" style="line-height: 10px; margin-right: 10px;">
            <p>Invoice - #{{ $transaction->id }}</p>
            <p>{{ \Carbon\Carbon::parse($transaction->created_at)->format('d F Y') }}</p>
        </div>
    </div>

    <div class="container p-2 mt-5">
        <table style="width: 100%;">
            <thead>
                <tr>
                    <th style="width: 30%"><b>Produk</b></th>
                    <th style="width: 20%"><b>Harga</b></th>
                    <th style="width: 20%"><b>Quantity</b></th>
                    <th style="width: 25%" class="text-end"><b>Sub Total</b></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transaction->detailTransactions as $item)
                    <tr>
                        <td>{{ $item->product->product_name }}</td>
                        <td>Rp. {{ number_format($item->product->price, 0, ',', '.') }}</td>
                        <td>{{ $item->qty }}</td>
                        <td class="text-end">
                            Rp. {{ number_format($item->product->price * $item->qty, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @php
        $totalBelanja = 0;
        foreach ($transaction->detailTransactions as $item) {
            $totalBelanja += $item->product->price * $item->qty;
        }
    @endphp

    <div class="container p-2 mt-5">
        <table style="width: 100%;">
            <thead>
                <tr>
                    <th style="width: 30%;">Point Digunakan</th>
                    <th style="width: 20%;">Kasir</th>
                    <th style="width: 20%;">Kembalian</th>
                    <th class="text-end" style="width: 25%;">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr style="height: auto;">
                    <td>{{ $transaction->customer->points }}</td>
                    <td>{{ $transaction->user->name ?? '-' }}</td>
                    <td>Rp. {{ number_format($transaction->change, 0, ',', '.') }}</td>
                    <td class="text-end align-top">
                        <div class="d-inline-block p-2 bg-dark text-white rounded" style="min-width: 250px;">
                            <p class="mb-1 text-start">TOTAL</p>
                            <div style="line-height: 1.5;">
                                <p class="mb-1 text-end" style="font-size: 25px;">
                                    Rp. {{ number_format($totalBelanja, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
