<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HariKerja;

class HariKerjaController extends Controller
{
    // daftar hari kerja
    public function index()
    {
        $hariKerjaList = HariKerja::orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")->get();
        return view('hari-kerja.index', compact('hariKerjaList'));
    }
    // akhir daftar hari kerja

    // form tambah hari kerja
    public function create()
    {
        $hariKerjaList = HariKerja::orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")->get();
        return view('hari-kerja.create', compact('hariKerjaList'));
    }
    // akhir form tambah hari kerja

    // simpan hari kerja
    public function store(Request $request)
    {
        // validasi input
        $request->validate([
            'hari'       => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jam_masuk'  => 'required',
            'jam_pulang' => 'required',
        ]);
        // akhir validasi input

        try {
            // cek hari terdaftar
            $exists = HariKerja::where('hari', $request->hari)->exists();
            if ($exists) {
                return redirect()->route('hari-kerja.gagal')->with('error', 'Hari ' . $request->hari . ' sudah ada dalam daftar hari kerja.');
            }
            // akhir cek hari terdaftar

            // tambah data ke database
            HariKerja::create([
                'hari'       => $request->hari,
                'jam_masuk'  => $request->jam_masuk,
                'jam_pulang' => $request->jam_pulang,
                'status'     => 'Aktif',
            ]);
            // akhir tambah data ke database

            return redirect()->route('hari-kerja.berhasil');
        } catch (\Exception $e) {
            return redirect()->route('hari-kerja.gagal')->with('error', $e->getMessage());
        }
    }
    // akhir simpan hari kerja

    // form edit hari kerja
    public function edit($id)
    {
        $hariKerja     = HariKerja::findOrFail($id);
        $hariKerjaList = HariKerja::orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")->get();
        return view('hari-kerja.edit', compact('hariKerja', 'hariKerjaList'));
    }
    // akhir form edit hari kerja

    // perbarui hari kerja
    public function update(Request $request, $id)
    {
        // validasi input
        $request->validate([
            'hari'       => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jam_masuk'  => 'required',
            'jam_pulang' => 'required',
        ]);
        // akhir validasi input

        try {
            $hariKerja = HariKerja::findOrFail($id);

            // cek hari ganda
            $exists = HariKerja::where('hari', $request->hari)
                ->where('id_hari_kerja', '!=', $id)
                ->exists();

            if ($exists) {
                return redirect()->route('hari-kerja.gagal')->with('error', 'Hari ' . $request->hari . ' sudah ada dalam daftar hari kerja.');
            }
            // akhir cek hari ganda

            // perbarui ke database
            $hariKerja->update([
                'hari'       => $request->hari,
                'jam_masuk'  => $request->jam_masuk,
                'jam_pulang' => $request->jam_pulang,
            ]);
            // akhir perbarui ke database

            return redirect()->route('hari-kerja.berhasil');
        } catch (\Exception $e) {
            return redirect()->route('hari-kerja.gagal')->with('error', $e->getMessage());
        }
    }
    // akhir perbarui hari kerja

    // hapus hari kerja
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
    // akhir hapus hari kerja

    // tampilan berhasil
    public function berhasil(Request $request)
    {
        $hariKerjaList = HariKerja::orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")->get();
        $isDelete = $request->query('action') === 'delete' || session('action') === 'delete';
        return view('hari-kerja.berhasil', compact('hariKerjaList', 'isDelete'));
    }
    // akhir tampilan berhasil

    // tampilan gagal
    public function gagal(Request $request)
    {
        $hariKerjaList = HariKerja::orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")->get();
        return view('hari-kerja.gagal', compact('hariKerjaList'));
    }
    // akhir tampilan gagal
}
