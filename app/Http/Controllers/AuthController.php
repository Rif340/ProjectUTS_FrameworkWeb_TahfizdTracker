<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login Guru & Admin
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Menampilkan halaman login khusus Murid
     */
    public function showLoginMuridForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login_murid');
    }

    /**
     * Proses autentikasi login (Email + Password + NIP / NISN)
     */
    public function login(Request $request)
    {
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

        $email = strtolower(trim($request->email));
        $password = $request->password;
        $identifier = trim($request->nip);

        // Cari user berdasarkan email
        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

        if (!$user) {
            return back()->withErrors(['login' => 'Akun dengan email tersebut tidak ditemukan.'])->withInput();
        }

        if ($user->status !== 'Aktif') {
            return back()->withErrors(['login' => 'Akun Anda sedang nonaktif. Silakan hubungi administrator.'])->withInput();
        }

        // Verifikasi password
        if (!Hash::check($password, $user->password)) {
            return back()->withErrors(['login' => 'Password yang Anda masukkan salah.'])->withInput();
        }

        // Verifikasi NIP / NISN sesuai Role
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

        // Login user ke session
        Auth::login($user);
        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        return redirect()->route('dashboard');
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect()->route('login');
    }
}
