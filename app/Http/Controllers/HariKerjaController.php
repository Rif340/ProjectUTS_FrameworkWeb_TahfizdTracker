<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HariKerja;

class HariKerjaController extends Controller
{
    /**
     * Tampilkan daftar hari kerja (Figma Node 618:1003).
     */
    public function index()
    {
        $hariKerjaList = HariKerja::orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")->get();
        return view('hari-kerja.index', compact('hariKerjaList'));
    }

    /**
     * Tampilkan form tambah hari kerja (Figma Node 625:3576).
     */
    public function create()
    {
        $hariKerjaList = HariKerja::orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")->get();
        return view('hari-kerja.create', compact('hariKerjaList'));
    }

    /**
     * Simpan hari kerja baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'hari'       => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jam_masuk'  => 'required',
            'jam_pulang' => 'required',
        ]);

        try {
            // Cek apakah hari sudah terdaftar
            $exists = HariKerja::where('hari', $request->hari)->exists();
            if ($exists) {
                return redirect()->route('hari-kerja.gagal')->with('error', 'Hari ' . $request->hari . ' sudah ada dalam daftar hari kerja.');
            }

            HariKerja::create([
                'hari'       => $request->hari,
                'jam_masuk'  => $request->jam_masuk,
                'jam_pulang' => $request->jam_pulang,
                'status'     => 'Aktif',
            ]);

            return redirect()->route('hari-kerja.berhasil');
        } catch (\Exception $e) {
            return redirect()->route('hari-kerja.gagal')->with('error', $e->getMessage());
        }
    }

    /**
     * Tampilkan form edit hari kerja (Figma Node 625:4612).
     */
    public function edit($id)
    {
        $hariKerja     = HariKerja::findOrFail($id);
        $hariKerjaList = HariKerja::orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")->get();
        return view('hari-kerja.edit', compact('hariKerja', 'hariKerjaList'));
    }

    /**
     * Perbarui data hari kerja.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'hari'       => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jam_masuk'  => 'required',
            'jam_pulang' => 'required',
        ]);

        try {
            $hariKerja = HariKerja::findOrFail($id);

            // Cek apakah hari sudah digunakan di record lain
            $exists = HariKerja::where('hari', $request->hari)
                ->where('id_hari_kerja', '!=', $id)
                ->exists();

            if ($exists) {
                return redirect()->route('hari-kerja.gagal')->with('error', 'Hari ' . $request->hari . ' sudah ada dalam daftar hari kerja.');
            }

            $hariKerja->update([
                'hari'       => $request->hari,
                'jam_masuk'  => $request->jam_masuk,
                'jam_pulang' => $request->jam_pulang,
            ]);

            return redirect()->route('hari-kerja.berhasil');
        } catch (\Exception $e) {
            return redirect()->route('hari-kerja.gagal')->with('error', $e->getMessage());
        }
    }

    /**
     * Hapus hari kerja (Figma Node 657:9841).
     */
    public function destroy($id)
    {
        try {
            $hariKerja = HariKerja::findOrFail($id);
            $hariKerja->delete();
            return redirect()->route('hari-kerja.berhasil', ['action' => 'delete']);
        } catch (\Exception $e) {
            return redirect()->route('hari-kerja.gagal')->with('error', 'Gagal menghapus data hari kerja.');
        }
    }

    /**
     * Halaman alert berhasil.
     */
    public function berhasil(Request $request)
    {
        $hariKerjaList = HariKerja::orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")->get();
        $isDelete = $request->query('action') === 'delete' || session('action') === 'delete';
        return view('hari-kerja.berhasil', compact('hariKerjaList', 'isDelete'));
    }

    /**
     * Halaman alert gagal.
     */
    public function gagal(Request $request)
    {
        $hariKerjaList = HariKerja::orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")->get();
        return view('hari-kerja.gagal', compact('hariKerjaList'));
    }
}
