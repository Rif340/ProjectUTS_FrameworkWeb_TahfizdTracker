<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admin;
use App\Models\Guru;
use App\Models\Murid;
use Illuminate\Support\Facades\Hash;

class PenggunaController extends Controller
{
    // daftar pengguna
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 10);
        $penggunaList = User::with(['admin', 'guru', 'murid'])
            ->orderBy('nama', 'asc')
            ->take($perPage)
            ->get();
        return view('pengguna.index', compact('penggunaList'));
    }
    // akhir daftar pengguna

    // form tambah pengguna
    public function create()
    {
        return view('pengguna.create');
    }
    // akhir form tambah pengguna

    // simpan pengguna
    public function store(Request $request)
    {
        // validasi input
        $request->validate([
            'nama'     => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s.,\'’\-]+$/'],
            'email'    => 'required|email|max:100',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:Admin,Guru,Murid',
            'nip_nisn' => ['nullable', 'string', 'max:18', 'regex:/^[0-9]+$/'],
            'no_hp'    => ['nullable', 'string', 'max:15', 'regex:/^[0-9]+$/'],
        ], [
            'nama.regex'     => 'Nama lengkap hanya boleh berisi huruf, spasi, dan gelar tanda baca.',
            'nip_nisn.regex' => 'NIP / NISN hanya boleh berisi angka.',
            'no_hp.regex'    => 'No. Handphone hanya boleh berisi angka.',
        ]);
        // akhir validasi input

        try {
            // cek email terdaftar
            $exists = User::where('email', $request->email)->exists();
            if ($exists) {
                return redirect()->route('pengguna.gagal')
                    ->with('error', 'Email ' . $request->email . ' sudah terdaftar.');
            }
            // akhir cek email terdaftar

            // buat akun pengguna
            $user = User::create([
                'nama'     => $request->nama,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => $request->role,
                'status'   => 'Aktif',
            ]);
            // akhir buat akun pengguna

            // simpan data peran pengguna
            $nipNisn = $request->nip_nisn ?: null;
            $noHp = $request->no_hp ?: null;
            if ($request->role === 'Admin') {
                if ($nipNisn && Admin::where('nip', $nipNisn)->exists()) {
                    $user->delete();
                    return redirect()->route('pengguna.gagal')
                        ->with('error', 'NIP ' . $nipNisn . ' sudah digunakan admin lain.');
                }
                Admin::create(['id_user' => $user->id_user, 'nip' => $nipNisn, 'qr_code' => $nipNisn, 'no_hp' => $noHp]);
            } elseif ($request->role === 'Guru') {
                if ($nipNisn && Guru::where('nip', $nipNisn)->exists()) {
                    $user->delete();
                    return redirect()->route('pengguna.gagal')
                        ->with('error', 'NIP ' . $nipNisn . ' sudah digunakan guru lain.');
                }
                Guru::create(['id_user' => $user->id_user, 'nip' => $nipNisn, 'qr_code' => $nipNisn, 'no_hp' => $noHp]);
            } elseif ($request->role === 'Murid') {
                if ($nipNisn && Murid::where('nisn', $nipNisn)->exists()) {
                    $user->delete();
                    return redirect()->route('pengguna.gagal')
                        ->with('error', 'NISN ' . $nipNisn . ' sudah digunakan murid lain.');
                }
                Murid::create(['id_user' => $user->id_user, 'nisn' => $nipNisn, 'qr_code' => $nipNisn, 'no_hp' => $noHp]);
            }
            // akhir simpan data peran pengguna

            \App\Services\QRCodeService::getOrGenerateUserQR($user);

            return redirect()->route('pengguna.berhasil');
        } catch (\Exception $e) {
            return redirect()->route('pengguna.gagal')->with('error', $e->getMessage());
        }
    }
    // akhir simpan pengguna

    // form edit pengguna
    public function edit($id)
    {
        $pengguna = User::with(['admin', 'guru', 'murid'])->findOrFail($id);
        return view('pengguna.edit', compact('pengguna'));
    }
    // akhir form edit pengguna

    // perbarui pengguna
    public function update(Request $request, $id)
    {
        // validasi input
        $request->validate([
            'nama'     => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s.,\'’\-]+$/'],
            'email'    => 'required|email|max:100',
            'role'     => 'required|in:Admin,Guru,Murid',
            'nip_nisn' => ['nullable', 'string', 'max:18', 'regex:/^[0-9]+$/'],
            'no_hp'    => ['nullable', 'string', 'max:15', 'regex:/^[0-9]+$/'],
            'password' => 'nullable|string|min:6',
        ], [
            'nama.regex'     => 'Nama lengkap hanya boleh berisi huruf, spasi, dan gelar tanda baca.',
            'nip_nisn.regex' => 'NIP / NISN hanya boleh berisi angka.',
            'no_hp.regex'    => 'No. Handphone hanya boleh berisi angka.',
        ]);
        // akhir validasi input

        try {
            $pengguna = User::with(['admin', 'guru', 'murid'])->findOrFail($id);

            // cek email ganda
            $exists = User::where('email', $request->email)
                ->where('id_user', '!=', $id)
                ->exists();
            if ($exists) {
                return redirect()->route('pengguna.gagal')
                    ->with('error', 'Email ' . $request->email . ' sudah digunakan pengguna lain.');
            }
            // akhir cek email ganda

            // perbarui akun pengguna
            $data = [
                'nama'  => $request->nama,
                'email' => $request->email,
                'role'  => $request->role,
            ];

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $pengguna->update($data);
            // akhir perbarui akun pengguna

            // perbarui data peran pengguna
            $nipNisn = $request->nip_nisn;
            $noHp = $request->no_hp ?: null;
            if ($request->role === 'Admin') {
                if ($pengguna->admin) {
                    $pengguna->admin->update(['nip' => $nipNisn, 'qr_code' => $nipNisn, 'no_hp' => $noHp]);
                } else {
                    Admin::create(['id_user' => $id, 'nip' => $nipNisn, 'qr_code' => $nipNisn, 'no_hp' => $noHp]);
                }
            } elseif ($request->role === 'Guru') {
                if ($pengguna->guru) {
                    $pengguna->guru->update(['nip' => $nipNisn, 'qr_code' => $nipNisn, 'no_hp' => $noHp]);
                } else {
                    Guru::create(['id_user' => $id, 'nip' => $nipNisn, 'qr_code' => $nipNisn, 'no_hp' => $noHp]);
                }
            } elseif ($request->role === 'Murid') {
                if ($pengguna->murid) {
                    $pengguna->murid->update(['nisn' => $nipNisn, 'qr_code' => $nipNisn, 'no_hp' => $noHp]);
                } else {
                    Murid::create(['id_user' => $id, 'nisn' => $nipNisn, 'qr_code' => $nipNisn, 'no_hp' => $noHp]);
                }
            }
            // akhir perbarui data peran pengguna

            \App\Services\QRCodeService::getOrGenerateUserQR($pengguna);

            return redirect()->route('pengguna.berhasil');
        } catch (\Exception $e) {
            return redirect()->route('pengguna.gagal')->with('error', $e->getMessage());
        }
    }
    // akhir perbarui pengguna

    // hapus pengguna
    public function destroy($id)
    {
        try {
            $pengguna = User::findOrFail($id);
            $pengguna->delete();
            return redirect()->route('pengguna.berhasil', ['action' => 'delete']);
        } catch (\Exception $e) {
            return redirect()->route('pengguna.gagal')->with('error', 'Gagal menghapus data pengguna.');
        }
    }
    // akhir hapus pengguna

    // tampilan berhasil
    public function berhasil(Request $request)
    {
        $isDelete = $request->query('action') === 'delete' || session('action') === 'delete';
        return view('pengguna.berhasil', compact('isDelete'));
    }
    // akhir tampilan berhasil

    // tampilan gagal
    public function gagal(Request $request)
    {
        return view('pengguna.gagal');
    }
    // akhir tampilan gagal
}
