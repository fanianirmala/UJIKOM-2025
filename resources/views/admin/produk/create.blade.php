@extends('template.sidebar')
@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins&display=swap');

    :root {
        --sidebar-icon-color: #757575;
        --sidebar-icon-size: 20px;
        --title-font-size: 30px;
        --title-font-color: #20142d;
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

    .form {
        border: 1px solid #bfbfbf !important;
    }

    .btn {
        color: white;
        width: 170px;
    }

</style>

<div class="container">
    <div class="nav d-flex">
        <a href="#"><i class="ti ti-home"></i></a><p><span> > </span> Produk</p>
    </div>

    <p class="title mb-3 mt-2">Tambah Produk</p>

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('admin.produk.store') }}" class="row g-3 mt-5" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <label for="productName" class="form-label">Nama Produk <span class="text-danger">*</span></label>
                <input type="text" class="form-control form @error('product_name') is-invalid @enderror" name="product_name">
                @error('product_name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="productImage" class="form-label">Gambar Produk <span class="text-danger">*</span></label>
                <input type="file" class="form-control form @error('product_image') is-invalid @enderror" id="image" name="product_image">
                @error('product_image')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            <div class="col-md-6 mt-3">
                <label for="price" class="form-label">Harga <span class="text-danger">*</span></label>
                <input type="number" class="form-control form @error('price') is-invalid @enderror" id="price" name="price">
                @error('price')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            <div class="col-md-6 mt-3">
                <label for="stock" class="form-label">Stok <span class="text-danger">*</span></label>
                <input type="number" class="form-control form @error('stock') is-invalid @enderror" id="stock" name="stock">
                @error('stock')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            <div class=" d-flex justify-content-end mt-3">
                <button type="submit" class="btn" style="background-color: #1e4db7; font-size: 14px;">Simpan</button>
            </div>
        </div>
    </form>
</div>

@endsection
