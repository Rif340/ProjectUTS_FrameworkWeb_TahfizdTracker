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
    /**
     * Tampilkan daftar pengguna (Figma Node 620:2600).
     * Join NIP/NISN dari tabel admin/guru/murid.
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 10);
        $penggunaList = User::with(['admin', 'guru', 'murid'])
            ->orderBy('nama', 'asc')
            ->take($perPage)
            ->get();
        return view('pengguna.index', compact('penggunaList'));
    }

    /**
     * Tampilkan form tambah pengguna (Figma Node 625:3040).
     */
    public function create()
    {
        return view('pengguna.create');
    }

    /**
     * Simpan pengguna baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama'     => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s.,\'’\-]+$/'],
            'email'    => 'required|email|max:100',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:Admin,Guru,Murid',
            'nip_nisn' => ['nullable', 'string', 'max:18', 'regex:/^[0-9]+$/'],
        ], [
            'nama.regex'     => 'Nama lengkap hanya boleh berisi huruf, spasi, dan gelar tanda baca.',
            'nip_nisn.regex' => 'NIP / NISN hanya boleh berisi angka.',
        ]);

        try {
            // Cek email sudah terdaftar
            $exists = User::where('email', $request->email)->exists();
            if ($exists) {
                return redirect()->route('pengguna.gagal')
                    ->with('error', 'Email ' . $request->email . ' sudah terdaftar.');
            }

            $user = User::create([
                'nama'     => $request->nama,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => $request->role,
                'status'   => 'Aktif',
            ]);

            // Buat record di tabel sesuai role + validasi NIP/NISN unik
            $nipNisn = $request->nip_nisn ?: null;
            if ($request->role === 'Admin') {
                if ($nipNisn && Admin::where('nip', $nipNisn)->exists()) {
                    $user->delete();
                    return redirect()->route('pengguna.gagal')
                        ->with('error', 'NIP ' . $nipNisn . ' sudah digunakan admin lain.');
                }
                Admin::create(['id_user' => $user->id_user, 'nip' => $nipNisn, 'qr_code' => $nipNisn]);
            } elseif ($request->role === 'Guru') {
                if ($nipNisn && Guru::where('nip', $nipNisn)->exists()) {
                    $user->delete();
                    return redirect()->route('pengguna.gagal')
                        ->with('error', 'NIP ' . $nipNisn . ' sudah digunakan guru lain.');
                }
                Guru::create(['id_user' => $user->id_user, 'nip' => $nipNisn, 'qr_code' => $nipNisn]);
            } elseif ($request->role === 'Murid') {
                if ($nipNisn && Murid::where('nisn', $nipNisn)->exists()) {
                    $user->delete();
                    return redirect()->route('pengguna.gagal')
                        ->with('error', 'NISN ' . $nipNisn . ' sudah digunakan murid lain.');
                }
                Murid::create(['id_user' => $user->id_user, 'nisn' => $nipNisn, 'qr_code' => $nipNisn]);
            }

            \App\Services\QRCodeService::getOrGenerateUserQR($user);

            return redirect()->route('pengguna.berhasil');
        } catch (\Exception $e) {
            return redirect()->route('pengguna.gagal')->with('error', $e->getMessage());
        }
    }

    /**
     * Tampilkan form edit pengguna (Figma Node 625:4901).
     */
    public function edit($id)
    {
        $pengguna = User::with(['admin', 'guru', 'murid'])->findOrFail($id);
        return view('pengguna.edit', compact('pengguna'));
    }

    /**
     * Perbarui data pengguna.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama'     => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s.,\'’\-]+$/'],
            'email'    => 'required|email|max:100',
            'role'     => 'required|in:Admin,Guru,Murid',
            'nip_nisn' => ['nullable', 'string', 'max:18', 'regex:/^[0-9]+$/'],
            'password' => 'nullable|string|min:6',
        ], [
            'nama.regex'     => 'Nama lengkap hanya boleh berisi huruf, spasi, dan gelar tanda baca.',
            'nip_nisn.regex' => 'NIP / NISN hanya boleh berisi angka.',
        ]);

        try {
            $pengguna = User::with(['admin', 'guru', 'murid'])->findOrFail($id);

            // Cek email sudah dipakai pengguna lain
            $exists = User::where('email', $request->email)
                ->where('id_user', '!=', $id)
                ->exists();
            if ($exists) {
                return redirect()->route('pengguna.gagal')
                    ->with('error', 'Email ' . $request->email . ' sudah digunakan pengguna lain.');
            }

            $data = [
                'nama'  => $request->nama,
                'email' => $request->email,
                'role'  => $request->role,
            ];

            // Hanya update password jika diisi
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $pengguna->update($data);

            // Update NIP/NISN di tabel relasi sesuai role
            $nipNisn = $request->nip_nisn;
            if ($request->role === 'Admin') {
                if ($pengguna->admin) {
                    $pengguna->admin->update(['nip' => $nipNisn, 'qr_code' => $nipNisn]);
                } else {
                    Admin::create(['id_user' => $id, 'nip' => $nipNisn, 'qr_code' => $nipNisn]);
                }
            } elseif ($request->role === 'Guru') {
                if ($pengguna->guru) {
                    $pengguna->guru->update(['nip' => $nipNisn, 'qr_code' => $nipNisn]);
                } else {
                    Guru::create(['id_user' => $id, 'nip' => $nipNisn, 'qr_code' => $nipNisn]);
                }
            } elseif ($request->role === 'Murid') {
                if ($pengguna->murid) {
                    $pengguna->murid->update(['nisn' => $nipNisn, 'qr_code' => $nipNisn]);
                } else {
                    Murid::create(['id_user' => $id, 'nisn' => $nipNisn, 'qr_code' => $nipNisn]);
                }
            }

            \App\Services\QRCodeService::getOrGenerateUserQR($pengguna);

            return redirect()->route('pengguna.berhasil');
        } catch (\Exception $e) {
            return redirect()->route('pengguna.gagal')->with('error', $e->getMessage());
        }
    }

    /**
     * Hapus pengguna.
     */
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

    /**
     * Halaman alert berhasil.
     */
    public function berhasil(Request $request)
    {
        $isDelete = $request->query('action') === 'delete' || session('action') === 'delete';
        return view('pengguna.berhasil', compact('isDelete'));
    }

    /**
     * Halaman alert gagal.
     */
    public function gagal(Request $request)
    {
        return view('pengguna.gagal');
    }
}
