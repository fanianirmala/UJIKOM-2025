@extends('template.sidebar')
@section('content')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    :root {
        --sidebar-icon-color: #757575;
        --sidebar-icon-size: 20px;
        --title-font-size: 30px;
        --title-font-color: #20142d;
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

    .container {
        width: 70%;
        position: relative;
        left: 80px;
        padding-right: 150px;
    }

    .chart-container {
        width: 80%;
        height: 300px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .chart-container canvas {
        height: 100% !important;
        max-height: 400px;
    }


</style>

<body>

<div class="nav d-flex">
    <a href="#"><i class="ti ti-home"></i></a><p><span> > </span> Dashboard</p>
</div>

<p class="title mb-3 mt-2">Dashboard</p>

<p class="mt-4">Selamat Datang, Administrator!</p>
<div class="container">
    <div class="chart-container">
        <canvas id="barChart"></canvas>
        <canvas id="pieChart"></canvas>
    </div>
</div>
</body>

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
