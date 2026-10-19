<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Admin;
use App\Models\Guru;
use App\Models\Murid;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman Profile pengguna (Figma Node 621:2785 & 621:2862).
     * Dapat diakses oleh semua role (Admin, Guru, Murid).
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Load relasi role terkait
        $detail = null;
        $roleLower = strtolower($user->role);

        if ($roleLower === 'admin') {
            $detail = Admin::where('id_user', $user->id_user)->first();
        } elseif ($roleLower === 'guru') {
            $detail = Guru::where('id_user', $user->id_user)->first();
        } elseif ($roleLower === 'murid') {
            $detail = Murid::where('id_user', $user->id_user)->first();
        }

        $qrData = \App\Services\QRCodeService::getOrGenerateUserQR($user, $detail);

        return view('profile.index', compact('user', 'detail', 'qrData'));
    }

    /**
     * Perbarui data profil pengguna (Figma Node 621:2862).
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $request->validate([
            'nama'     => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s.,\'’\-]+$/'],
            'email'    => 'required|email|max:100|unique:users,email,' . $user->id_user . ',id_user',
            'nip_nisn' => ['nullable', 'string', 'max:18', 'regex:/^[0-9]+$/'],
            'no_hp'    => ['nullable', 'string', 'max:15', 'regex:/^[0-9]+$/'],
            'foto'     => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'nama.regex'     => 'Nama lengkap hanya boleh berisi huruf, spasi, dan gelar tanda baca (tidak boleh angka).',
            'nip_nisn.regex' => 'NIP / NISN hanya boleh berisi angka.',
            'no_hp.regex'    => 'Nomor handphone hanya boleh berisi angka.',
        ]);

        try {
            // Update data user utama
            $user->nama = $request->nama;
            $user->email = $request->email;
            $user->save();

            // Role specific detail
            $roleLower = strtolower($user->role);
            $detail = null;

            if ($roleLower === 'admin') {
                $detail = Admin::firstOrCreate(['id_user' => $user->id_user]);
                if ($request->has('nip_nisn')) {
                    $detail->nip = $request->nip_nisn;
                    $detail->qr_code = $request->nip_nisn;
                }
            } elseif ($roleLower === 'guru') {
                $detail = Guru::firstOrCreate(['id_user' => $user->id_user]);
                if ($request->has('nip_nisn')) {
                    $detail->nip = $request->nip_nisn;
                    $detail->qr_code = $request->nip_nisn;
                }
            } elseif ($roleLower === 'murid') {
                $detail = Murid::firstOrCreate(['id_user' => $user->id_user]);
                if ($request->has('nip_nisn')) {
                    $detail->nisn = $request->nip_nisn;
                    $detail->qr_code = $request->nip_nisn;
                }
            }

            if ($detail) {
                if ($request->filled('no_hp')) {
                    $detail->no_hp = $request->no_hp;
                }

                // Upload Foto Formal jika ada (disimpan di storage/app/public/profile)
                if ($request->hasFile('foto')) {
                    $file = $request->file('foto');
                    $filename = 'foto_' . $user->id_user . '_' . time() . '.' . $file->getClientOriginalExtension();
                    
                    // Hapus foto lama jika tersimpan di storage
                    if (!empty($detail->foto) && str_starts_with($detail->foto, 'storage/')) {
                        $oldStoragePath = str_replace('storage/', '', $detail->foto);
                        Storage::disk('public')->delete($oldStoragePath);
                    }

                    $path = Storage::disk('public')->putFileAs('profile', $file, $filename);
                    $detail->foto = 'storage/' . $path;
                }

                $detail->save();

                // Generate / perbarui QR code di storage sesuai NIP/NISN terbaru
                \App\Services\QRCodeService::getOrGenerateUserQR($user, $detail);
            }

            return redirect()->route('profile.index')->with('success_modal', 'Data Profile Berhasil Di Perbarui');
        } catch (\Exception $e) {
            return redirect()->route('profile.index')->with('error_modal', 'Gagal memperbarui profil: ' . $e->getMessage());
        }
    }
}
