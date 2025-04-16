<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css" />
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="icon" href="{{ asset('images/logo.png') }}">
    <title>FlexyLite - Dashboard</title>
</head>
<body>
    <div class="wrapper">
        <aside id="sidebar">
            <div class="h-100">
                <div class="sidebar-logo">
                    <img src="{{ asset('images/logo-sidebar.png') }}" alt="Logo" style="width: 120px; height: auto;">
                    <ul class="sidebar-nav">
                        @php
                            $route = Route::currentRouteName();
                        @endphp

                        <li class="sidebar-item">
                            @if(auth()->user()->role == 'admin')
                                <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ $route == 'admin.dashboard' ? 'active' : '' }}">
                                    <i class="ti ti-layout-dashboard-filled" style="font-size: 20px;"></i>
                                    Dashboard
                                </a>
                            @elseif(auth()->user()->role == 'petugas')
                                <a href="{{ route('petugas.dashboard') }}" class="sidebar-link {{ $route == 'petugas.dashboard' ? 'active' : '' }}">
                                    <i class="ti ti-layout-dashboard-filled" style="font-size: 20px; color: #757575;"></i>
                                    Dashboard
                                </a>
                            @endif
                        </li>

                        {{-- MENU WAT ADMIN --}}
                        @if(auth()->user()->role == 'admin')
                            <li class="sidebar-item">
                                <a href="{{ route('admin.produk') }}" class="sidebar-link {{ in_array(Route::currentRouteName(), ['admin.produk', 'admin.produk.create', 'admin.produk.edit']) ? 'active' : '' }}">
                                    <i class="ti ti-building-store" style="font-size: 20px; color: #757575;"></i>
                                    Produk
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a href="{{ route('admin.pembelian') }}" class="sidebar-link {{ $route == 'admin.pembelian' ? 'active' : '' }}">
                                    <i class="ti ti-shopping-cart-filled" style="font-size: 20px; color: #757575;"></i>
                                    Penjualan
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a href="{{ route('admin.user') }}" class="sidebar-link {{ in_array(Route::currentRouteName(), ['admin.user', 'admin.user.create', 'admin.user.edit']) ? 'active' : '' }}">
                                    <i class="ti ti-users" style="font-size: 20px; color: #757575;"></i>
                                    User
                                </a>
                            </li>
                        @endif

                        {{-- MENU WAT PETUGAS --}}
                        @if(auth()->user()->role == 'petugas')
                            <li class="sidebar-item">
                                <a href="{{ route('petugas.produk') }}" class="sidebar-link {{ $route == 'petugas.produk' ? 'active' : '' }}">
                                    <i class="ti ti-building-store" style="font-size: 20px; color: #757575;"></i>
                                    Produk
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a href="{{ route('petugas.pembelian') }}" class="sidebar-link {{ in_array(Route::currentRouteName(), ['petugas.pembelian', 'petugas.create.penjualan', 'petugas.sale.create', 'petugas.checkout', 'petugas.member.checkout', 'petugas.member.struk', 'petugas.non-member.struk']) ? 'active' : '' }}">
                                    <i class="ti ti-shopping-cart-filled" style="font-size: 20px; color: #757575;"></i>
                                    Penjualan
                                </a>
                            </li>
                        @endif
                    </ul>
            </div>
        </aside>

        <div class="main">
            <nav class="navbar navbar-expand px-3 border-bottom d-flex" style="justify-content: space-between; align-items: center;">
                <form class="d-flex" role="search">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="ti ti-search" style="font-size: 20px"></i>
                        </span>
                        <input class="form-control" type="search" placeholder="Search" aria-label="Search">
                    </div>
                    <button class="btn btn-outline-success" type="submit" style="display: none;"></button>
                </form>

                <div class="con d-flex align-items-center">
                    <div class="img-profile dropdown">
                        <img src="{{ asset('images/profile.jpg') }}" class="rounded-circle dropdown-toggle" width="50" height="50"
                            id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-right"></i>Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <main class="content">
                @yield( 'content')
            </main>

        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>

    <script>
        let table = new DataTable('#myTable');
    </script>
</body>
</html>
