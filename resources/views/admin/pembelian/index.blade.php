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
    <a href="#"><i class="ti ti-home"></i></a><p><span> > </span> Penjualan</p>
</div>

<p class="title mb-3 mt-2">Penjualan</p>

<a href="#" class="addBtn">Export Penjualan (.xlsx)</a>

<div class="card p-3" style="width: 1035px;">
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
                    <tr>
                        <td>1</td>
                        <td>
                            Non Member
                        </td>
                        <td>15-04-25</td>
                        <td>Rp. 28.000</td>
                        <td>
                            Petugas
                        </td>
                        <td style="text-align: center">
                            <button class="viewBtn" data-bs-toggle="modal" data-bs-target="#updateModal">Lihat</button>

                            {{-- MODAL EDIT --}}
                            <div class="modal fade mt-5" id="updateModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel" style="font-size: 15px;">Detail Penjualan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 15px;"></button>
                                        </div>
                                        <div class="modal-body" style="text-align: left; line-height: 1;">
                                            <div class="d-flex" style="justify-content: space-between">
                                                <p>Member Status : Member</p>
                                                <p>Bergabung Sejak : 10-04-25</p>
                                            </div>
                                            <p>No. HP : 089582376</p>
                                            <p class="mb-5">Point Member : 0</p>

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
                                                        <tr>
                                                            <td>Kucing Lucu</td>
                                                            <td>4</td>
                                                            <td>Rp. 80.000</td>
                                                            <td>Rp. 240.000</td>
                                                        </tr>
                                                    <tr>
                                                        <td colspan="2"></td>
                                                        <td><strong>Total</strong></td>
                                                        <td><strong>Rp. 240.000</strong></td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <div class="info" style="text-align: center">
                                                <p class="mt-5">Dibuat pada : 15-04-25</p>
                                                <p>Oleh : Petugas</p>
                                            </div>
                                            <div class="modal-footer" style="float: right">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <a href="#" class="unduhBtn">Unduh Bukti</a>
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
