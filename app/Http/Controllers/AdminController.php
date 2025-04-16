<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\TransactionExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\DetailTransaction;

class AdminController extends Controller
{
    public function dashboard()
    {
        $barChart = Transaction::selectRaw('DATE(created_at) as tanggal, COUNT(*) as jumlah_transaksi')->groupBy('tanggal')->orderBy('tanggal')->get();

        $pieChart = DetailTransaction::join('products', 'detail_transactions.product_id', '=', 'products.id')->selectRaw('products.product_name, SUM(qty) as total_terjual')->groupBy('products.product_name')->orderBy('total_terjual', 'desc')->get();

        return view('admin.dashboard', [
            'barChart' => $barChart,
            'pieChart' => $pieChart,
        ]);
    }


    public function userIndex()
    {
        $user = User::orderBy('created_at', 'desc')->get();
        return view('admin.user.index', compact('user'));
    }

    public function userCreate()
    {
        return view('admin.user.create');
    }

    public function userStore(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin,petugas',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.user')->with('success', 'Berhasil menambahkan data User!');
    }

    public function userEdit($id)
    {
        $user = User::find($id);
        return view('admin.user.edit', compact('user'));
    }

    public function userUpdate(Request $request, $id)
    {
        $user = User::find($id);

        $request->validate([
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin,petugas',
        ]);

        if($request->password){
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'password' => Hash::make($request->password),
            ]);
        }else{
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
            ]);
        }
        return redirect()->route('admin.user')->with('success', 'Berhasil mengubah data User!');
    }

    public function userDestroy(string $id)
    {
        User::where('id', $id)->delete();

        // $user = User::findOrFail($id);

        // if ($user->role === 'admin') {
        //     return redirect()->route('admin.user')->with('error', 'Role admin tidak bisa dihapus!');
        // }

        // $user->delete();

        return redirect()->route('admin.user')->with('success', 'Berhasil menghapus data User!');
    }

    public function produkIndex()
    {
        $products = Product::all();
        return view('admin.produk.index', compact('products'));
    }

    public function produkCreate()
    {
        return view('admin.produk.create');
    }

    public function produkStore(Request $request)
    {
        $request->validate([
            'product_name' => 'required|min:3',
            'product_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
        ]);

        if ($request->hasFile('product_image')) {
            $gambarProduk = $request->file('product_image');
            $namaFile = time() . '.' . $gambarProduk->getClientOriginalExtension();
            $gambarProduk->move(public_path('uploads'), $namaFile);
        } else {
            $namaFile = null;
        }

        Product::create([
            'product_name' => $request->product_name,
            'product_image' => $namaFile,
            'price' => $request->price,
            'stock' => $request->stock,
        ]);

        return redirect()->route('admin.produk')->with('success', 'Berhasil menambahkan produk baru!');
    }

    public function produkEdit($id)
    {
        $products = Product::find($id);
        return view('admin.produk.edit', compact('products'));
    }

    public function produkStok(Request $request, $id)
    {
        $request->validate([
            'stock' => 'required|integer',
        ]);

        $produk = Product::find($id);

        $produk->update([
            'stock' => $request->stock,
        ]);

        return redirect()->route('admin.produk')->with('success', 'Berhasil memperbarui stock Produk!');

    }

    public function produkUpdate(Request $request, $id)
    {
        $request->validate([
            'product_name' => 'required|min:3',
            'product_image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
        ]);

        $produk = Product::find($id);

        if ($request->hasFile('product_image')) {
            if ($produk->product_image && file_exists(public_path('uploads/' . $produk->product_image))) {
                unlink(public_path('uploads/' . $produk->product_image));
            }

            $gambarProduk = $request->file('product_image');
            $namaFile = time() . '.' . $gambarProduk->getClientOriginalExtension();
            $gambarProduk->move(public_path('uploads'), $namaFile);
        } else {
            $namaFile = $produk->product_image;
        }

        $produk->update([
            'product_name' => $request->product_name,
            'product_image' => $namaFile,
            'price' => $request->price,
            'stock' => $request->stock,
        ]);

        return redirect()->route('admin.produk')->with('success', 'Berhasil memperbarui data Produk!');
    }

    public function produkDestroy(string $id)
    {
        Product::where('id', $id)->delete();

        return redirect()->route('admin.produk')->with('success', 'Berhasil menghapus data Produk!');
    }

    public function pembelianIndex()
    {
        $transactions = Transaction::with(['customer', 'user', 'detailTransactions.product'])->orderBy('created_at', 'desc')->get();
        return view('admin.pembelian.index', compact('transactions'));
    }

    public function unduhStruk($id)
    {
        $transaction = Transaction::with(['customer', 'user', 'detailTransactions.product'])->findOrFail($id);

        $totalAfterDiscount = session('totalAfterDiscount');

        $html = view('petugas.pdf.receipt', compact('transaction', 'totalAfterDiscount'))->render();

        $pdf = Pdf::loadHTML($html);

        return $pdf->download('receipt-' . $id . '.pdf');
    }

    public function exportTransactions()
    {
        return Excel::download(new TransactionExport, 'transactions.xlsx');
    }
}