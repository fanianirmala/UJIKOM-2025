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
        float: right;
        background-color: var(--bold-primary-button-color);
        font-size: var(--button-font-size);
        color: var(--font-color);
        padding: var(--button-padding);
        border-radius: var(--button-border-radius);
        margin-bottom: 15px;
        margin-right: 10px;
    }

    .editBtn{
        color: var(--font-color);
        background-color: var(--warning-button-color);
        padding: var(--button-padding);
        border: none;
        border-radius: var(--button-border-radius);
    }

    .deleteBtn{
        color: var(--font-color);
        background-color: var(--danger-button-color);
        padding: var(--button-padding);
        border: none;
        border-radius: var(--button-border-radius);
    }

    .updateBtn{
        color: var(--font-color);
        background-color: var(--primary-button-color);
        padding: var(--button-padding);
        border: none;
        border-radius: var(--button-border-radius);
    }

    .modal {
        z-index: 1050;
    }

    .modal-backdrop {
        z-index: 1030;
    }

    .navbar {
        z-index: 1000;
    }

</style>


<div class="nav d-flex">
    <a href="{{ route('petugas.produk') }}"><i class="ti ti-home"></i></a><p><span> > </span> Produk</p>
</div>

<p class="title mb-3 mt-2">Produk</p>

<div class="card p-3" style="width: 1220px;">
    <div class="table-responsive">
        <table class="table" id="myTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Gambar Produk</th>
                    <th>Nama Produk</th>
                    <th>Harga</th>
                    <th style="text-align: center">Stok</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach ($products as $product)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td><img src="{{ asset('uploads/' . $product->product_image) }}" width="100"</td>
                        <td>{{ $product['product_name'] }}</td>
                        <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                        <td style="text-align: center">{{ $product['stock'] }}</td>
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
