@extends('template.sidebar')
@section('content')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    :root {
        --sidebar-hover-active: #1a9bfc;
        --sidebar-hover: #f5f5f5;
        --sidebar-icon-color: #757575;
        --sidebar-font-color: #d1d5d7;
        --heading-font-color: #11142d;
        --title-font-color: #20142d;
        --bold-primary-button-color: #1e4db7;
        --danger-button-color: #fc4b6c;
        --primary-button-color: #1a9bfc;
        --warning-button-color: #fdc90f;
        --heading-table-color: #808b99;
        --field-table-color: #adb8c0;
        --border-color: #ced4da;
        --sidebar-icon-size: 20px;
        --route-font-size: 20px;
        --title-font-size: 30px;
        --placeholder-font-size: 15px;
        --background-color: #ffffff;
        --sidebar-font-size: 15px;
        --font-color-global: #ffffff;
        --border-radius: 10px;
        --padding: 10px;
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

</style>

<body>

<div class="nav d-flex">
    <a href="{{ route('petugas.dashboard') }}"><i class="ti ti-home"></i></a><p><span> > </span> Dashboard</p>
</div>

<p class="title mb-3 mt-2">Dashboard</p>

<div class="card p-3" style="width: 1220px;">
    <div class="header p-2" style="font-size: 20px; font-weight: bold;">
        Selamat Datang, Petugas!
    </div>
    <div class="card text-center">
        <div class="card-header p-3 text-body-secondary">
            Total Penjualan Hari ini ({{ \Carbon\Carbon::parse($today)->format('d M Y') }})
        </div>

        <div class="card-body">
            <h5 class="card-title mb-3 mt-3" style="font-size: 30px;">
                {{ $penjualanHariIni }}
            </h5>
            <p class="card-text mb-3">Jumlah total penjualan yang terjadi hari ini.</p>
        </div>

        <div class="card-footer text-body-secondary p-3">
            @if ($penjualanHariIni > 0)
                Terakhir diperbarui: {{ \Carbon\Carbon::parse($lastPenjualan->tanggal_penjualan)->format('d M Y H:i') }}
            @else
                Tidak ada data penjualan hari ini.
            @endif
        </div>
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
