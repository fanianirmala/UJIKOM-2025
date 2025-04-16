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
        margin-right: 30px;
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

    .form{
        border: 1px solid #bfbfbf !important;
    }

</style>


<div class="nav d-flex">
    <a href="{{ route('admin.produk') }}"><i class="ti ti-home"></i></a><p><span> > </span> Produk</p>
</div>

<p class="title mb-3 mt-2">Produk</p>

<a href="{{ route('admin.produk.create') }}" class="addBtn">Tambah Produk</a>

<div class="card p-3" style="width: 1205px;">
    <div class="table-responsive">
        <table class="table" id="myTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Gambar Produk</th>
                    <th>Nama Produk</th>
                    <th>Harga</th>
                    <th style="text-align: center">Stok</th>
                    <th style="text-align: center">Aksi</th>
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
                        <td style="text-align: center">
                            <a href="{{ route('admin.produk.edit', $product['id']) }}" class="editBtn">Edit</a>
                            <button class="updateBtn" data-bs-toggle="modal" data-bs-target="#updateModal{{ $product['id'] }}">Update Stok</button>
                            <!-- Modal -->
                            <div class="modal fade mt-5" id="updateModal{{ $product['id'] }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Update Stok Produk</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                    <form action="{{ route('admin.produk.stok', $product['id']) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="mb-3">
                                            <label for="productName" class="form-label text-start d-block">Nama Produk <span class="text-danger">*</span></label>
                                            <input class="form-control" type="text" value="{{ $product['product_name'] }}" aria-label="Disabled input example" disabled readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label for="stock" class="form-label text-start d-block">Stok <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control form" id="stock" name="stock" value="{{ $product['stock'] }}" required>
                                        </div>

                                        <div class="footer" style="float: right;">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                        </div>
                                    </form>
                                    </div>
                                </div>
                                </div>
                            </div>
                            <form action="{{ route('admin.produk.destroy', ['id' => $product['id']]) }}" class="d-inline" method="post" onsubmit="return confirm('Apakah anda yakin ingin menghapus Data ini?')">
                                @method('delete')
                                @csrf
                                <button class="deleteBtn">
                                    Delete
                                </button>
                            </form>
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
