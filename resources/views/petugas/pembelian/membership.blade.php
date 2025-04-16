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
        --primary-button-color: #1a9bfc;
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

    .nav {
        color: var(--sidebar-icon-color);
    }

    .ti {
        font-size: var(--sidebar-icon-size);
        color: var(--sidebar-icon-color)
    }

    .title {
        font-weight: bold;
        font-size: var(--title-font-size);
        color: var(--title-font-color);
    }

    .form {
        border: 1px solid var(--border-color) !important;
        color: #98a3a6;
    }

    .card-custom {
        display: flex;
        flex-direction: row;
        width: 100%;
    }

    .card-left {
        width: 50%;
        padding: 20px;
    }

    .card-right {
        width: 50%;
        padding: 20px;
    }

    td {
        padding: 15px;
    }
</style>

<div class="nav d-flex">
    <a href="{{ route('petugas.pembelian') }}"><i class="ti ti-home"></i></a>
    <p><span> > </span> Pembelian</p>
</div>

<p class="title mb-3 mt-2">Penjualan</p>

<div class="card p-1 card-custom" style="width: 1220px;">
    <div class="card-left">
        <table border="1" style="width: 100%; height: 100%;">
            <thead>
                <tr>
                    <th style="padding: 10px;">Nama Produk</th>
                    <th style="padding: 10px;">QTY</th>
                    <th style="padding: 10px;">Harga</th>
                    <th style="padding: 10px;">Sub Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                <tr>
                    <td>{{ $item['product_name'] }}</td>
                    <td>{{ $item['quantity'] }}</td>
                    <td>Rp. {{ number_format($item['price'], 0, ',', '.') }}</td>
                    <td>Rp. {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                </tr>
                @endforeach
                <tr style="font-weight: bold; background-color: #f9f9f9;">
                    <td colspan="2"></td>
                    <td style="padding: 10px;">Total Harga</td>
                    <td style="padding: 10px;" id="totalHarga" data-total-harga="{{ $total }}">
                        Rp. {{ number_format($total, 0, ',', '.') }}
                    </td>
                </tr>
                <tr style="font-weight: bold;">
                    <td colspan="2"></td>
                    <td style="padding: 10px;">Total Bayar</td>
                    <td style="padding: 10px;">
                        Rp. {{ number_format(session('total_price'), 0, ',', '.') }}
                    </td>
                </tr>
                <tr style="font-weight: bold;">
                    <td colspan="2"></td>
                    <td style="padding: 10px;">Point Akan Didapat</td>
                    <td style="padding: 10px;">{{ ($totalPointAkanDidapat) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="card-right">
        @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
        @endif

        <form action="{{ route('petugas.member.proses') }}" method="POST">
            @csrf
            <div class="col-md-6 w-100">
                <label for="namaMember" class="form-label">Nama Member (identitas) <span class="text-danger">*</span></label>
                <input type="text" class="form-control form" name="name" value="{{ session('member_name') }}">
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6 w-100 mt-3">
                <label for="points" class="form-label">Point <span class="text-danger">*</span></label>
                <input type="number" class="form-control form" name="points" id="pointsInput" value="{{ session('member_points', 0) }}" disabled readonly>
            </div>

            <div class="d-flex">
                <input class="form-check-input mt-3" type="checkbox" id="usePointsCheckbox">
                <p style="margin-top: 13px; margin-left: 10px; color: #757575;">Gunakan poin</p>
            </div>

            <input type="hidden" name="total_bayar" id="totalBayarInput" value="{{ session('total_price') }}">
            <input type="hidden" name="harga_pure" id="hargaAwalInput" value="{{ $total }}">
            <input type="hidden" name="use_points" id="usePointsHidden" value="0">

            <button type="submit" class="btn w-100 mt-3" style="background-color: #1e4db7; color: #ffffff;">Selanjutnya</button>
        </form>
    </div>
</div>

<script>
    const usePointsCheckbox = document.getElementById('usePointsCheckbox');
    const pointsInput = document.getElementById('pointsInput');
    const totalHargaEl = document.getElementById('totalHarga');
    const totalBayarEl = document.getElementById('totalBayar');
    const usePointsHidden = document.getElementById('usePointsHidden');
    const totalBayarInput = document.getElementById('totalBayarInput');
    const hargaAwalInput = document.getElementById('hargaAwalInput');

    const nilaiTukarPerPoint = 1;
    const totalHargaAwal = parseInt(totalHargaEl.dataset.totalHarga);

    usePointsCheckbox.addEventListener('change', function () {
        const usePoints = this.checked;
        const points = parseInt(pointsInput.value) || 0;

        if (usePoints && points === 0) {
            alert("Tidak ada point! Point tidak bisa digunakan di pembelian pertama");
            this.checked = false;
            return;
        }

        let hargaSetelahPoin = totalHargaAwal;

        if (usePoints) {
            const potongan = points * nilaiTukarPerPoint;
            hargaSetelahPoin = Math.max(totalHargaAwal - potongan, 0);
            usePointsHidden.value = 1;
        } else {
            usePointsHidden.value = 0;
            hargaSetelahPoin = totalHargaAwal;
        }

        totalHargaEl.innerText = 'Rp. ' + hargaSetelahPoin.toLocaleString('id-ID');

        totalBayarEl.innerText = 'Rp. ' + totalBayarEl.dataset.totalBayar;

        totalBayarInput.value = totalBayarEl.dataset.totalBayar;
        hargaAwalInput.value = totalHargaAwal;
    });
</script>


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
