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
        border: 1px solid var(--border-color) !important;
        color: #98a3a6;
    }

    .card-custom {
        display: flex;
        flex-direction: row;
        width: 100%;
        /* max-width: 1050px; */
    }

    .card-left {
        width: 50%;
        padding: 20px;
    }

    .card-right {
        width: 50%;
        padding: 20px;
    }

</style>

<div class="nav d-flex">
    <a href="{{ route('petugas.pembelian') }}"><i class="ti ti-home"></i></a><p><span> > </span> Pembelian</p>
</div>

<p class="title mb-3 mt-2">Penjualan</p>

<div class="card p-1 card-custom" style="width: 1220px">
    <div class="card-left">
        <h5 class="mb-3">Produk yang dipilih</h5>
        @foreach($items as $item)
            <p style="font-size: 13px;">{{ $item['product_name'] }}</p>
            <div class="d-flex justify-content-between" style="font-size: 13px;">
                <p>Rp. {{ number_format($item['price'], 0, ',', '.') }} x {{ $item['quantity'] }}</p>
                <strong style="margin-right: 70px;">Rp. {{ number_format($item['subtotal'], 0, ',', '.') }}</strong>
            </div>

            <input type="hidden" name="produk[]" value="{{ $item['produk_id'] }}">
            <input type="hidden" name="nama_produk[]" value="{{ $item['product_name'] }}">
            <input type="hidden" name="harga[]" value="{{ $item['price'] }}">
            <input type="hidden" name="jumlah[]" value="{{ $item['quantity'] }}">
            <input type="hidden" name="subtotal[]" value="{{ $item['subtotal'] }}">
        @endforeach
        {{-- SUBTOTAL KESELURUHAN PRODUK --}}
        <div class="subtotalsum d-flex justify-content-between mt-3" style="margin-right: 70px; font-size: 20px;">
            <strong>Total</strong><strong> Rp. {{ number_format(array_sum(array_column($items, 'subtotal')), 0, ',', '.') }} </strong>
        </div>
    </div>

    <div class="card-right">

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('petugas.checkout') }}" method="POST" id="checkoutForm">
            @csrf
            <div class="mb-3">
                <label for="inputState" class="form-label">Member Status <span style="color: red; font-size: 13px;">Dapat juga membuat member</span></label>
                <select id="memberStatus" class="form-select form" name="member_status">
                    <option selected value="">Choose...</option>
                    <option value="non-member">Bukan Member</option>
                    <option value="member">Member</option>
                </select>
            </div>

            <div class="mb-3" id="phoneField" style="display: none;">
                <label for="phone_number" class="form-label">No Telepon <span style="color: red; font-size: 13px;"> (daftar/gunakan member)</span></label>
                <input type="number" class="form-control form @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number">
                @error('phone_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <input type="hidden" id="grand_total" value="{{ $total }}">

            <div class="mb-3">
                <label for="total_price" class="form-label">Total Bayar</label>
                <input type="text" class="form-control" id="total_price" name="total_price" placeholder="Rp.">

                <div id="alertPembayaran" class="invalid-feedback d-block mt-2" style="display: none;"></div>
            </div>

            <button type="submit" class="btn btn-primary w-100">Proses Pembelian</button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const inputTotal = document.getElementById('total_price');
        const alertBox = document.getElementById('alertPembayaran');
        const grandTotal = parseInt(document.getElementById('grand_total').value);

        inputTotal.addEventListener('input', function () {
            let raw = this.value.replace(/[^\d]/g, '');
            let formatted = new Intl.NumberFormat('id-ID').format(raw);
            this.value = formatted;

            let totalInput = parseInt(raw || 0);

            if (totalInput < grandTotal) {
                alertBox.style.display = 'block';
                alertBox.innerText = 'Pembayaran kurang dari total harga.';
                inputTotal.classList.add('is-invalid');
            } else if (totalInput > grandTotal) {
                alertBox.style.display = 'block';
                alertBox.innerText = 'Pembayaran lebih dari total, kembalian akan dihitung.';
                inputTotal.classList.add('is-invalid');
            } else {
                alertBox.style.display = 'none';
                alertBox.innerText = '';
                inputTotal.classList.remove('is-invalid');
            }
        });

        document.getElementById('checkoutForm').addEventListener('submit', function (e) {
            let raw = inputTotal.value.replace(/[^\d]/g, '');
            let totalInput = parseInt(raw || 0);

            if (totalInput < grandTotal) {
                alertBox.style.display = 'block';
                alertBox.innerText = 'Pembayaran kurang dari total harga.';
                inputTotal.classList.add('is-invalid');
                e.preventDefault(); // ini sekarang bakal jalan!
            }
        });

    });
</script>

<script>
    document.getElementById("memberStatus").addEventListener("change", function() {
        var phoneField = document.getElementById("phoneField");
        if (this.value === "member") {
            phoneField.style.display = "block"; // Tampilkan input nomor telepon
        } else {
            phoneField.style.display = "none"; // Sembunyikan jika bukan member
        }
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
