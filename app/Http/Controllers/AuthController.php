<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Tampilan login
     */
    public function login()
    {
        if (Auth::check()) {
            if (Auth::user()->role === 'admin') {
                return redirect()->route('transaksi.packing-list');
            }
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Proses autentikasi masuk
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::with('status')->where('username', $credentials['username'])->first();

        // BAGIAN C.1: Login ditolak jika status->level != 1
        if ($user && (!$user->status || (int) $user->status->level !== 1)) {
            $statusName = $user->status ? $user->status->nama_status : 'Non-Aktif';
            return back()->withErrors([
                'username' => "Akun Anda sedang {$statusName}. Hubungi Owner.",
            ])->withInput($request->only('username'));
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $redirectRoute = Auth::user()->role === 'admin' ? 'transaksi.packing-list' : 'dashboard';
            return redirect()->intended(route($redirectRoute))->with('success', 'Selamat datang kembali, ' . Auth::user()->username . '!');
        }

        return back()->withErrors([
            'username' => 'Username atau password yang Anda masukkan tidak valid.',
        ])->withInput($request->only('username'));
    }

    /**
     * Proses logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar dari sistem.');
    }
}
