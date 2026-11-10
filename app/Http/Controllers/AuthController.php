<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // halaman login guru dan admin
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }
    // akhir halaman login guru dan admin

    // halaman login khusus murid
    public function showLoginMuridForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login_murid');
    }
    // akhir halaman login khusus murid

    // proses autentikasi login
    public function login(Request $request)
    {
        // validasi form login
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'nip' => 'required',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'nip.required' => 'NIP / NISN wajib diisi.',
        ]);
        // akhir validasi form login

        $email = strtolower(trim($request->email));
        $password = $request->password;
        $identifier = trim($request->nip);

        // cari data pengguna
        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

        if (!$user) {
            return back()->withErrors(['login' => 'Akun dengan email tersebut tidak ditemukan.'])->withInput();
        }

        if ($user->status !== 'Aktif') {
            return back()->withErrors(['login' => 'Akun Anda sedang nonaktif. Silakan hubungi administrator.'])->withInput();
        }
        // akhir cari data pengguna

        // verifikasi kata sandi
        if (!Hash::check($password, $user->password)) {
            return back()->withErrors(['login' => 'Password yang Anda masukkan salah.'])->withInput();
        }
        // akhir verifikasi kata sandi

        // verifikasi nomor identitas
        $isIdentifierValid = false;

        if ($user->role === 'Admin') {
            $isIdentifierValid = ($user->admin && $user->admin->nip === $identifier);
        } elseif ($user->role === 'Guru') {
            $isIdentifierValid = ($user->guru && $user->guru->nip === $identifier);
        } elseif ($user->role === 'Murid') {
            $isIdentifierValid = ($user->murid && $user->murid->nisn === $identifier);
        }

        if (!$isIdentifierValid) {
            $expectedLabel = ($user->role === 'Murid') ? 'NISN' : 'NIP';
            return back()->withErrors(['login' => "$expectedLabel tidak sesuai dengan akun Anda."])->withInput();
        }
        // akhir verifikasi nomor identitas

        // proses masuk ke sesi
        Auth::login($user);
        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        return redirect()->route('dashboard');
        // akhir proses masuk ke sesi
    }
    // akhir proses autentikasi login

    // keluar dari sesi akun
    public function logout(Request $request)
    {
        Auth::logout();
        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect()->route('login');
    }
    // akhir keluar dari sesi akun
}
