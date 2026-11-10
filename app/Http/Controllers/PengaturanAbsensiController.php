<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengaturanAbsensi;

class PengaturanAbsensiController extends Controller
{
    // daftar pengaturan absensi
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 10);
        $lokasiList = PengaturanAbsensi::orderBy('id_pengaturan', 'asc')->paginate($perPage)->appends($request->except('page'));

        return view('absensi.pengaturan.index', compact('lokasiList', 'perPage'));
    }
    // akhir daftar pengaturan absensi

    // simpan lokasi absensi
    public function store(Request $request)
    {
        // validasi input
        $request->validate([
            'nama_lokasi'  => 'required|string|max:100',
            'latitude'     => 'required|numeric|between:-90,90',
            'longitude'    => 'required|numeric|between:-180,180',
            'radius_meter' => 'required|integer|min:1',
        ], [
            'nama_lokasi.required'  => 'Nama lokasi wajib diisi.',
            'latitude.required'     => 'Latitude wajib diisi.',
            'longitude.required'    => 'Longitude wajib diisi.',
            'radius_meter.required' => 'Radius meter wajib diisi.',
            'radius_meter.min'      => 'Radius minimal 1 meter.',
        ]);
        // akhir validasi input

        try {
            // simpan ke database
            PengaturanAbsensi::create([
                'nama_lokasi'  => $request->nama_lokasi,
                'latitude'     => $request->latitude,
                'longitude'    => $request->longitude,
                'radius_meter' => $request->radius_meter,
                'status'       => 'Aktif',
            ]);
            // akhir simpan ke database

            return redirect()->route('absensi.pengaturan.index')
                ->with('success', 'Lokasi batas absensi berhasil ditambahkan.')
                ->with('action', 'save');
        } catch (\Exception $e) {
            return redirect()->route('absensi.pengaturan.index')
                ->with('error', 'Gagal menambahkan lokasi: ' . $e->getMessage());
        }
    }
    // akhir simpan lokasi absensi

    // perbarui lokasi absensi
    public function update(Request $request, $id)
    {
        $lokasi = PengaturanAbsensi::findOrFail($id);

        // validasi input
        $request->validate([
            'nama_lokasi'  => 'required|string|max:100',
            'latitude'     => 'required|numeric|between:-90,90',
            'longitude'    => 'required|numeric|between:-180,180',
            'radius_meter' => 'required|integer|min:1',
        ], [
            'nama_lokasi.required'  => 'Nama lokasi wajib diisi.',
            'latitude.required'     => 'Latitude wajib diisi.',
            'longitude.required'    => 'Longitude wajib diisi.',
            'radius_meter.required' => 'Radius meter wajib diisi.',
            'radius_meter.min'      => 'Radius minimal 1 meter.',
        ]);
        // akhir validasi input

        try {
            // perbarui ke database
            $lokasi->update([
                'nama_lokasi'  => $request->nama_lokasi,
                'latitude'     => $request->latitude,
                'longitude'    => $request->longitude,
                'radius_meter' => $request->radius_meter,
            ]);
            // akhir perbarui ke database

            return redirect()->route('absensi.pengaturan.index')
                ->with('success', 'Lokasi batas absensi berhasil diperbarui.')
                ->with('action', 'save');
        } catch (\Exception $e) {
            return redirect()->route('absensi.pengaturan.index')
                ->with('error', 'Gagal memperbarui lokasi: ' . $e->getMessage());
        }
    }
    // akhir perbarui lokasi absensi

    // hapus lokasi absensi
    public function destroy($id)
    {
        try {
            $lokasi = PengaturanAbsensi::findOrFail($id);
            $lokasi->delete();

            return redirect()->route('absensi.pengaturan.index')
                ->with('success', 'Lokasi batas absensi berhasil dihapus.')
                ->with('action', 'delete');
        } catch (\Exception $e) {
            return redirect()->route('absensi.pengaturan.index')
                ->with('error', 'Gagal menghapus lokasi: ' . $e->getMessage());
        }
    }
    // akhir hapus lokasi absensi
}
