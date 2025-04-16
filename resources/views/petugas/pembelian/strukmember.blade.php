@extends('template.sidebar')
@section('content')
<div class="card p-4" style="width: 1045px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);">
    <div class="d-flex justify-content-between">
        <div class="d-flex" style="gap: 5px;">
            <a href="#" class="btn btn-secondary" style="height: 40px;">Kembali</a>
            <a href="#" class="btn btn-primary" style="height: 40px;">Unduh</a>
        </div>
        <div class="info text-end mt-2" style="line-height: 10px; margin-right: 10px;">
            <p>Invoice - #980</p>
            <p>10-04-25</p>
        </div>
    </div>

    <div class="info-member mt-4 p-2" style="line-height: 10px;">
        <p><strong>1928199</strong></p>
        <p>Member Sejak : 10-04-25</p>
        <p>Member Point : 97898</p>
    </div>

    <div class="container p-2 mt-3">
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
                    <tr>
                        <td>Kucing Lucu</td>
                        <td>Rp. 80.000</td>
                        <td>82</td>
                        <td class="text-end">
                            Rp. 80.000
                        </td>
                    </tr>
            </tbody>
        </table>
    </div>

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
                    <td>
                        28 Poin
                    </td>
                    <td>Petugas</td>
                    <td>Rp. 80.000</td>
                    <td class="text-end align-top">
                        <div class="d-inline-block p-2 bg-dark text-white rounded" style="min-width: 250px;">
                            <p class="mb-1 text-start">TOTAL</p>
                            <div style="line-height: 1.5;">
                                <p class="mb-1 text-end">
                                    <s>Rp. 80.000</s>
                                </p>
                                <p class="mb-0 text-end">
                                    <b style="font-size: 20px;">
                                        Rp. 240.000
                                    </b>
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
