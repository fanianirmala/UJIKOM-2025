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

    .form{
        border: 1px solid #bfbfbf !important;
    }

    .btn{
        color: white;
        width: 170px;
    }

    .form-input{
        width: 50%;
    }
</style>



<div class="nav d-flex">
    <a href="#"><i class="ti ti-home"></i></a><p><span> > </span> User</p>
</div>

<p class="title mb-3 mt-2">Tambah User</p>

@if(session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif

<form action="{{ route('admin.user.store') }}" class="row g-3 mt-5" method="POST" style="width: 1240px;">
    @csrf
    <div class="form-input">
        <label for="inputNama" class="form-label">Nama <span style="color: red;">*</span></label>
        <input type="text" class="form-control form @error('name') is-invalid @enderror" name="name">
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="form-input">
        <label for="inputEmail" class="form-label">Email <span style="color: red;">*</span></label>
        <input type="email" class="form-control form @error('email') is-invalid @enderror" name="email">
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-input">
        <label for="inputState" class="form-label">Role <span style="color: red;">*</span></label>
        <select id="inputState" class="form-select form" name="role">
            <option selected value="admin">Choose...</option>
            <option value="admin">Admin</option>
            <option value="petugas">Employee</option>
        </select>
    </div>
    <div class="form-input">
        <label for="inputPassword" class="form-label">Password <span style="color: red;">*</span></label>
        <input type="password" class="form-control form @error('password') is-invalid @enderror" name="password">
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class=" d-flex justify-content-end">
        <button type="submit" class="btn" style="background-color: #1e4db7; font-size: 14px;">Simpan</button>
    </div>
  </form>

@endsection
