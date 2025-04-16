<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Customer;
use App\Models\DetailTransaction;
use App\Models\Transaction;

class PetugasController extends Controller
{
    public function dashboard()
    {
        return view('petugas.dashboard');
    }

    public function produkIndex()
    {
        $products = Product::all();
        return view('petugas.produk.index', compact('products'));
    }

    public function pembelianIndex()
    {
        $transactions = Transaction::with(['customer', 'user', 'detailTransactions.product'])->orderBy('created_at', 'desc')->get();
        return view('petugas.pembelian.index', compact('transactions'));
    }

    public function createPenjualan()
    {
        $products = Product::all();
        return view('petugas.pembelian.create', compact('products'));
    }

    public function saleCreate(Request $request)
    {
        $produkTerpilih = $request->input('produk_id');
        $jumlahProduk = $request->input('jumlah');

        if (!$produkTerpilih || !$jumlahProduk) {
            return back()->with('failed', 'Tidak ada produk yang dipilih.');
        }

        $products = Product::whereIn('id', $produkTerpilih)->get()->keyBy('id');

        $items = [];
        $total = 0;

        foreach ($produkTerpilih as $index => $produkId) {
            if (isset($products[$produkId])) {
                $product = $products[$produkId];
                $jumlah = $jumlahProduk[$index];

                if ($jumlah > 0) {
                    $items[] = [
                        'produk_id' => $produkId,
                        'product_name' => $product->product_name,
                        'quantity' => $jumlah,
                        'price' => $product->price,
                        'subtotal' => $product->price * $jumlah,
                    ];

                    $total += $product->price * $jumlah;
                }
            }
        }

        session([
            'produk_terpilih' => $produkTerpilih,
            'jumlah_produk' => $jumlahProduk,
            'total' => $total,
        ]);

        if (empty($items)) {
            return back()->with('failed', 'Harap pilih setidaknya satu produk.');
        }

        return view('petugas.pembelian.checkout', compact('items', 'total'));
    }

    public function nonMemberCheckout(Request $request)
    {
        $request->merge([
            'total_price' => str_replace('.', '', $request->total_price)
        ]);

        $request->validate([
            'member_status' => 'required',
            'phone_number' => $request->member_status === 'member' ? 'required|numeric' : '',
            'total_price' => 'required|numeric',
        ]);

        $total = $request->total_price;
        $subtotal = session('total');

        if ($request->member_status === 'member') {
            $member = Customer::where('phone_number', $request->phone_number)->where('member_status', 'member')->first();

            if (!$member) {
                session([
                    'phone_number' => $request->phone_number,
                    'member_status' => $request->member_status,
                    'total_price' => $total,
                ]);

                return redirect()->route('petugas.member.checkout')->with('warning', 'Nomor belum terdaftar sebagai member, silakan lengkapi data untuk mendaftar!');
            }

            session([
                'phone_number' => $request->phone_number,
                'total_price' => $total,
                'member_status' => $request->member_status,
                'member_name' => $member->name,
                'member_points' => $member->points,
            ]);

            return redirect()->route('petugas.member.checkout')->with('success', 'Silakan lengkapi data member di halaman checkout!');
        }

        $nonMember = Customer::create([
            'name' => 'Non Member',
            'phone_number' => $request->phone_number ?? null,
            'member_status' => $request->member_status,
            'joined_at' => now(),
            'points' => 0,
        ]);

        $totalBayar = (float) $request->input('total_price');

        $kembalian = $totalBayar - $subtotal;

        $transaction = Transaction::create([
            'user_id' => auth()->id(),
            'customer_id' => $nonMember->id,
            'total_price' => $total,
            'change' => $kembalian,
            'created_at' => now(),
            'customer_pay' => $totalBayar,
        ]);

        $produkTerpilih = session('produk_terpilih');
        $jumlahProduk = session('jumlah_produk');

        if (!$produkTerpilih || !$jumlahProduk) {
            return back()->with('failed', 'Tidak ada produk yang dipilih atau jumlah produk tidak tersedia.');
        }

        $produkTerpilih = array_values(array_filter($produkTerpilih, function ($index) use ($jumlahProduk) {
            return isset($jumlahProduk[$index]) && $jumlahProduk[$index] > 0;
        }, ARRAY_FILTER_USE_KEY));

        $jumlahProduk = array_values(array_filter($jumlahProduk, fn($qty) => $qty > 0));

        session([
            'produk_terpilih' => $produkTerpilih,
            'jumlah_produk' => $jumlahProduk,
            'total_price' => $total,
        ]);

        foreach ($produkTerpilih as $index => $produkId) {
            $jumlah = $jumlahProduk[$index] ?? 0;
            $product = Product::find($produkId);

            if ($product && $product->stock >= $jumlah) {
                $product->decrement('stock', $jumlah);

                DetailTransaction::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $produkId,
                    'qty' => $jumlah,
                    'price' => $product->price,
                    'subtotal' => $product->price * $jumlah,
                ]);
            } else {
                return back()->with('failed', 'Stok produk ' . ($product->product_name ?? 'tidak ditemukan') . ' tidak mencukupi.');
            }
        }

        session()->forget(['produk_terpilih', 'jumlah_produk']);

        return redirect()->route('petugas.non-member.struk' , $transaction->id)->with('success', 'Penjualan non-member berhasil diproses!');
    }

    public function nonMemberStruk($id)
    {
        $transaction = Transaction::with(['customer', 'user', 'detailTransactions.product'])->findOrFail($id);

        $kembalian = session('kembalian');

        return view('petugas.pembelian.struknonmember', compact('transaction','kembalian'));
    }
}
