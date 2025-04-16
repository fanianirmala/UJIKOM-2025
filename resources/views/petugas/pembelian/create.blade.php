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

    .card-item{
        width: 1000px;
        height: auto;
        margin: 5px;
    }

    .fixed-button {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        background: white;
        padding: 15px 0;
        box-shadow: 0px -2px 5px rgba(0, 0, 0, 0.1);
        text-align: center;
        z-index: 1000;
    }

    .nextBtn{
        background-color: var(--bold-primary-button-color);
        color: var(--font-color);
        border: none;
        padding: var(--button-padding);
        border-radius: var(--button-border-radius);
        font-size: var(--button-font-size);
    }

</style>


<div class="nav d-flex">
    <a href="{{ route('petugas.produk') }}"><i class="ti ti-home"></i></a><p><span> > </span> Pembelian</p>
</div>

<p class="title mb-3 mt-2"> Tambah Penjualan</p>
<form action="{{ route('petugas.sale.create') }}" method="POST">
    @csrf
    <div class="container">
        <div class="card p-3">
            <div class="row justify-content-center">
                @foreach($products as $product)
                    <div class="col-md-3 d-flex justify-content-center">
                        <div class="card-item card p-3 d-flex flex-column">
                            <img src="{{ asset('uploads/' . $product['product_image']) }}" class="card-img-top fixed-img">
                            <div class="card-body text-center">
                                <p><strong>{{ $product['product_name'] }}</strong></p>
                                <p>Stok <span class="stok" data-stock="{{ $product['stock'] }}">{{ $product['stock'] }}</span></p>
                                <p style="font-size: 13px;">
                                    <strong>Rp. <span class="harga" data-price="{{ $product['price'] }}">{{ number_format($product['price'], 0, ',', '.') }}</span></strong>
                                </p>
                                <div class="d-flex justify-content-center align-items-center">
                                    <button type="button" class="btn minus">-</button>
                                    <span class="mx-2 jumlah">0</span>
                                    <button type="button" class="btn plus">+</button>
                                </div>
                                <p class="mt-2" style="font-size: 13px;">Sub Total <strong>Rp. <span class="subtotal">0</span></strong></p>

                                <input type="hidden" name="produk_id[]" value="{{ $product->id }}">
                                <input type="hidden" name="jumlah[]" class="jumlah-input" value="0" min="0">

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="fixed-button">
            <button type="submit" class="nextBtn">Selanjutnya</button>
        </div>
    </div>
</form>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".card-item").forEach(card => {
            let plusBtn = card.querySelector(".plus");
            let minusBtn = card.querySelector(".minus");

            let jumlahSpan = card.querySelector(".jumlah");
            let stokSpan = card.querySelector(".stok");
            let subtotalSpan = card.querySelector(".subtotal");
            let hargaSpan = card.querySelector(".harga");

            let stok = parseInt(stokSpan.dataset.stock);
            let harga = parseInt(hargaSpan.dataset.price);
            let jumlah = 0;

            plusBtn.addEventListener("click", function () {
                if (stok > 0) {
                    jumlah++;
                    stok--;
                    jumlahSpan.textContent = jumlah;
                    stokSpan.textContent = stok;
                    subtotalSpan.textContent = (jumlah * harga).toLocaleString("id-ID");
                    card.querySelector(".jumlah-input").value = jumlah;
                } else {
                    alert("Stok habis!");
                }
            });

            minusBtn.addEventListener("click", function () {
                if (jumlah > 0) {
                    jumlah--;
                    stok++;
                    jumlahSpan.textContent = jumlah;
                    stokSpan.textContent = stok;
                    subtotalSpan.textContent = (jumlah * harga).toLocaleString("id-ID");
                    card.querySelector(".jumlah-input").value = jumlah;
                }
            });
        });
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
