<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class LoginController extends Controller
{
    public function loginForm()
    {
        return view('login');
    }

    public function loginAuth(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password'=> 'required',
        ]);

        $user = $request->only(['email', 'password']);

        if (Auth::attempt($user)) {
            session()->flash('success', 'Login Berhasil!');

            $role = Auth::user()->role;

            if ($role == 'admin'){
                return redirect()->route('admin.dashboard');
            } elseif($role == 'petugas'){
                return redirect()->route('petugas.dashboard');
            }
        } else{
            return redirect()->back()->with('failed', 'Proses login gagal!, Silahkan coba kembali.');
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login')->with('success', 'Anda Telah Logout!');
    }
}
