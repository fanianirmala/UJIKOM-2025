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
    <a href="{{ route('admin.dashboard') }}"><i class="ti ti-home"></i></a><p><span> > </span> Dashboard</p>
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

<script>
    // Data dari controller (dalam bentuk JSON)
    const barChartData = @json($barChart);
    const pieChartData = @json($pieChart);

    const ctxBar = document.getElementById('barChart').getContext('2d');
    const ctxPie = document.getElementById('pieChart').getContext('2d');

    // ===== Bar Chart (Jumlah transaksi per tanggal) =====
    const barLabels = barChartData.map(item => item.tanggal);
    const barData = barChartData.map(item => item.jumlah_transaksi);

    new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: barLabels,
            datasets: [{
                label: 'Jumlah Transaksi',
                data: barData,
                backgroundColor: 'rgba(75, 192, 192, 0.5)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Jumlah Transaksi'
                    }
                },
                x: {
                    ticks: { maxRotation: 45, minRotation: 45 },
                    grid: { display: false },
                    title: {
                        display: true,
                        text: 'Tanggal'
                    }
                }
            },
            plugins: {
                legend: { display: true }
            }
        }
    });

    // ===== Pie Chart (Jumlah produk terjual per produk) =====
    const pieLabels = pieChartData.map(item => item.product_name);
    const pieData = pieChartData.map(item => item.total_terjual);
    const pieColors = [
        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
        '#9966FF', '#FF9F40', '#C9CBCF', '#FF4500',
        '#00CED1', '#ADFF2F', '#D2691E', '#6495ED'
    ];

    new Chart(ctxPie, {
        type: 'pie',
        data: {
            labels: pieLabels,
            datasets: [{
                data: pieData,
                backgroundColor: pieColors.slice(0, pieLabels.length)
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            }
        }
    });
</script>



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
