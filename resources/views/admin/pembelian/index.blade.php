@extends('template.sidebar')
@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins&display=swap');

    :root {
        --sidebar-icon-color: #757575;
        --sidebar-icon-size: 20px;
        --title-font-size: 30px;
        --title-font-color: #20142d;
        --heading-table-color: #79808b;
        --padding: 10px;
        --field-table-color: #7f8690;
        --bold-primary-button-color: #1e4db7;
        --button-font-size: 15px;
        --font-color: #ffffff;
        --button-padding: 10px 15px;
        --button-border-radius: 5px;
        --warning-button-color: #fdc90f;
        --danger-button-color: #fc4b6c;
        --text-table: 13px;
    }

    body {
        font-family: 'Poppins', sans-serif;
        margin: 0;
    }

    .nav{
        color: var(--sidebar-icon-color);
    }

    .ti {
        font-size: var(--sidebar-icon-size);
        color: var(--sidebar-icon-color)
    }

    .title{
        font-weight: bold;
        font-size: var(--title-font-size);
        color: var(--title-font-color);
    }

    .table{
        font-size: var(--text-table);
    }

    .table th{
        padding: var(--padding);
    }

    .table td{
        padding: var(--padding);
    }

    .addBtn{
        display: inline-block;
        background-color: var(--bold-primary-button-color);
        font-size: var(--button-font-size);
        color: var(--font-color);
        padding: var(--button-padding);
        border-radius: var(--button-border-radius);
        margin-bottom: 15px;
        margin-right: 30px;
    }

    .viewBtn{
        color: var(--font-color);
        background-color: var(--warning-button-color);
        padding: var(--button-padding);
        border: none;
        border-radius: var(--button-border-radius);
    }

    .unduhBtn{
        color: var(--font-color);
        background-color: var(--primary-button-color);
        padding: var(--button-padding);
        border: none;
        border-radius: var(--button-border-radius);
    }
</style>


<div class="nav d-flex">
    <a href="{{ route('admin.produk') }}"><i class="ti ti-home"></i></a><p><span> > </span> Penjualan</p>
</div>

<p class="title mb-3 mt-2">Penjualan</p>

<a href="{{ route('admin.export.transactions') }}" class="addBtn">Export Penjualan (.xlsx)</a>

<div class="card p-3" style="width: 1220px;">
    <div class="table-responsive">
        <table class="table" id="myTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Pelanggan</th>
                    <th>Tanggal Penjualan</th>
                    <th>Total Harga</th>
                    <th>Dibuat Oleh</th>
                    <th style="text-align: center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach ($transactions as $transaction)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>
                            {{ $transaction->customer ? $transaction->customer->name : '-' }}
                        </td>
                        <td>{{ \Carbon\Carbon::parse($transaction->created_at)->format('d-m-Y') }}</td>
                        <td>Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</td>
                        <td>
                            {{ $transaction->user ? $transaction->user->name : 'User tidak diketahui' }}
                        </td>
                        <td style="text-align: center">
                            <button class="viewBtn" data-bs-toggle="modal" data-bs-target="#updateModal{{ $transaction->id }}">Lihat</button>

                            {{-- MODAL --}}
                            <div class="modal fade mt-5" id="updateModal{{ $transaction->id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel" style="font-size: 15px;">Detail Penjualan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 15px;"></button>
                                        </div>
                                        <div class="modal-body" style="text-align: left; line-height: 1;">
                                            <div class="d-flex" style="justify-content: space-between">
                                                <p>Member Status : {{ $transaction->customer && $transaction->customer->name === 'Non Member' ? 'Non Member' : 'Member' }}</p>
                                                <p>Bergabung Sejak : {{ $transaction->created_at->format('d-m-Y') }}</p>
                                            </div>
                                            <p>No. HP : {{ $transaction->customer->phone_number ?? '-' }}</p>
                                            <p class="mb-5">Point Member : {{ $transaction->customer->points ?? '0' }}</p>

                                            <table style="width: 100%">
                                                <thead>
                                                    <tr>
                                                        <td>Nama Produk</td>
                                                        <td>Qty</td>
                                                        <td>Harga</td>
                                                        <td>Sub Total</td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $total = 0; @endphp
                                                    @foreach ($transaction->detailTransactions->where('qty', '>', 0) as $detailTransaction)
                                                        <tr>
                                                            <td>{{ $detailTransaction->product->product_name ?? '-' }}</td>
                                                            <td>{{ $detailTransaction->qty }}</td>
                                                            <td>Rp. {{ number_format($detailTransaction->price, 0, ',', '.') }}</td>
                                                            <td>Rp. {{ number_format($detailTransaction->subtotal, 0 ,',', '.') }}</td>
                                                        </tr>
                                                        @php $total += $detailTransaction->subtotal; @endphp
                                                    @endforeach
                                                    <tr>
                                                        <td colspan="2"></td>
                                                        <td><strong>Total</strong></td>
                                                        <td><strong>Rp. {{ number_format($total, 0, ',', '.') }}</strong></td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <div class="info" style="text-align: center">
                                                <p class="mt-5">Dibuat pada : {{ $transaction->created_at }}</p>
                                                <p>Oleh : {{ $transaction->user->name ?? 'Petugas'}}</p>
                                            </div>
                                            <div class="modal-footer" style="float: right">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('admin.unduh.struk', $transaction->id) }}" class="unduhBtn">Unduh Bukti</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success'))
    <script>
        Swal.fire({
            text: "{{ session('success') }}",
            icon: "success",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        });
    </script>
@endif

@if(session('failed'))
    <script>
        Swal.fire({
            text: "{{ session('failed') }}",
            icon: "error",
            confirmButtonColor: "#d33",
            confirmButtonText: "OK",
        });
    </script>
@endif

@endsection
