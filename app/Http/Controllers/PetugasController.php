<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Customer;
use App\Models\DetailTransaction;
use App\Models\Transaction;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\TransactionExport;
use Maatwebsite\Excel\Facades\Excel;

class PetugasController extends Controller
{
    public function dashboard()
    {
        $today = Carbon::today();

        $penjualanHariIni = Transaction::whereDate('created_at', $today)->count();

        $lastPenjualan = Transaction::whereDate('created_at', $today)->orderBy('created_at', 'desc')->first();

        return view('petugas.dashboard', compact('penjualanHariIni', 'today', 'lastPenjualan'));
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

    public function membership()
    {
        $produkTerpilih = session('produk_terpilih');
        $jumlahProduk = session('jumlah_produk');
        $totalBayar = session('total_price');

        if (!$produkTerpilih || !$jumlahProduk) {
            return back()->with('failed', 'Tidak ada produk yang dipilih.');
        }

        session([
            'produk_terpilih' => $produkTerpilih,
            'jumlah_produk' => $jumlahProduk,
            'total_price' => $totalBayar,
        ]);

        $produkDetails = Product::whereIn('id', $produkTerpilih)->get()->keyBy('id');

        $items = [];
        $total = 0;

        foreach ($produkTerpilih as $index => $produkId) {
            if (isset($produkDetails[$produkId])) {
                $product = $produkDetails[$produkId];
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

        if (empty($items)) {
            return back()->with('failed', 'Harap pilih setidaknya satu produk.');
        }

        $phoneNumber = session('phone_number');
        $memberStatus = session('member_status');

        $memberName = null;
        $memberPoints = 0;

        if ($phoneNumber && $memberStatus === 'member') {
            $member = Customer::where('phone_number', $phoneNumber)->where('member_status', 'member')->first();

            if ($member) {
                $memberName = $member->name;
                $memberPoints = $member->points;
            }
        }

        session([
            'member_name' => $memberName,
            'member_points' => $memberPoints,
        ]);

        $totalPointAkanDidapat = $total / 100;

        return view('petugas.pembelian.membership', compact(
            'items',
            'total',
            'memberName',
            'memberPoints',
            'phoneNumber',
            'memberStatus',
            'totalPointAkanDidapat',
        ));
    }

    public function memberCheckout(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $produkTerpilih = session('produk_terpilih');
        $jumlahProduk = session('jumlah_produk');
        $memberStatus = session('member_status');
        $phoneNumber = session('phone_number');

        if (!$produkTerpilih || !$jumlahProduk) {
            return back()->with('failed', 'Data produk tidak lengkap.');
        }

        $produkTerpilih = array_values(array_filter($produkTerpilih, function ($index) use ($jumlahProduk) {
            return isset($jumlahProduk[$index]) && $jumlahProduk[$index] > 0;
        }, ARRAY_FILTER_USE_KEY));

        $jumlahProduk = array_values(array_filter($jumlahProduk, fn($qty) => $qty > 0));

        if (count($produkTerpilih) === 0) {
            return back()->with('failed', 'Tidak ada produk valid yang dipilih.');
        }

        if ($memberStatus === 'member' && $phoneNumber) {
            $customer = Customer::firstOrCreate(
                ['phone_number' => $phoneNumber, 'member_status' => 'member'],
                ['name' => $request->name, 'joined_at' => now(), 'points' => 0]
            );
        } else {
            $customer = Customer::create([
                'name' => $request->name,
                'phone_number' => null,
                'member_status' => 'non-member',
                'joined_at' => now(),
                'points' => 0,
            ]);
        }

        $products = Product::whereIn('id', $produkTerpilih)->get()->keyBy('id');
        $total = 0;

        foreach ($produkTerpilih as $index => $produkId) {
            $jumlah = $jumlahProduk[$index];
            $product = $products[$produkId] ?? null;

            if ($product) {
                $total += $product->price * $jumlah;
            }
        }

        if ($total <= 0) {
            return back()->with('failed', 'Total harga tidak valid.');
        }

        $totalAfterDiscount = $total;

        $poinDipakai = 0;
        if ($customer->member_status === 'member' && $request->input('use_points') == 1) {
            $availablePoints = $customer->points;
            $nilaiTukarPerPoint = 1; // 1 poin = Rp1
            $diskon = min($availablePoints * $nilaiTukarPerPoint, $total);
            $poinDipakai = floor($diskon / $nilaiTukarPerPoint);
            $totalAfterDiscount = $total - $diskon;

            $total -= $diskon;

            if ($poinDipakai > 0) {
                $customer->points = 0;
                $customer->save();
            }
        }

        session()->put('poin_digunakan', $poinDipakai);
        session()->put('total_after_discount', $totalAfterDiscount);

        $totalBayar = (float) $request->input('total_bayar');
        $kembalian = $totalBayar - $total;

        $transaction = Transaction::create([
            'user_id' => auth()->id(),
            'customer_id' => $customer->id,
            'total_price' => $total,
            'change' => $kembalian,
            'created_at' => now(),
            'discount_price' => $totalAfterDiscount,
            'point_used' => $poinDipakai,
            'customer_pay' => $totalBayar,
        ]);

        $totalPoint = 0;

        foreach ($produkTerpilih as $index => $produkId) {
            $jumlah = $jumlahProduk[$index];
            $product = $products[$produkId] ?? null;

            if (!$product || $jumlah <= 0) {
                continue;
            }

            if ($product->stock < $jumlah) {
                return back()->with('failed', 'Stok produk ' . $product->product_name . ' tidak mencukupi.');
            }

            $product->decrement('stock', $jumlah);
            $subtotal = $product->price * $jumlah;

            DetailTransaction::create([
                'transaction_id' => $transaction->id,
                'product_id' => $produkId,
                'qty' => $jumlah,
                'price' => $product->price,
                'subtotal' => $subtotal,
            ]);

            $totalPoint += $subtotal;
        }

        if ($customer->member_status === 'member') {
            $earnedPoint = floor($totalPoint / 100);
            $customer->increment('points', $earnedPoint);
        }

        session()->forget(['produk_terpilih', 'jumlah_produk', 'phone_number', 'total_price', 'member_status']);

        return redirect()->route('petugas.member.struk', $transaction->id)
            ->with('success', 'Transaksi berhasil!')
            ->with('kembalian', $kembalian)
            ->with('total', $total) 
            ->with('totalAfterDiscount', $totalAfterDiscount);
    }

    public function memberStruk($id)
    {
        $transaction = Transaction::with(['customer', 'user', 'detailTransactions.product'])->findOrFail($id);

        $total = session('total');
        $totalAfterDiscount = session('totalAfterDiscount');
        $kembalian = session('kembalian');

        return view('petugas.pembelian.strukmember', compact('transaction', 'total', 'totalAfterDiscount', 'kembalian'));
    }

    public function unduhStruk($id)
    {
        $transaction = Transaction::with(['customer', 'user', 'detailTransactions.product'])->findOrFail($id);

        $html = view('petugas.pdf.receipt', compact('transaction'))->render();

        $pdf = Pdf::loadHTML($html);

        return $pdf->download('receipt-' . $id . '.pdf');
    }

    public function exportTransactions()
    {
        return Excel::download(new TransactionExport, 'transactions.xlsx');
    }
}
