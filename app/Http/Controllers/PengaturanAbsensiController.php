<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengaturanAbsensi;

class PengaturanAbsensiController extends Controller
{
    /**
     * Tampilkan Pengaturan Absensi / Jarak Batas Absensi (Figma Node 709:72997).
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 10);
        $lokasiList = PengaturanAbsensi::orderBy('id_pengaturan', 'asc')->paginate($perPage)->appends($request->except('page'));

        return view('absensi.pengaturan.index', compact('lokasiList', 'perPage'));
    }

    /**
     * Simpan lokasi batas absensi baru.
     */
    public function store(Request $request)
    {
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

        try {
            PengaturanAbsensi::create([
                'nama_lokasi'  => $request->nama_lokasi,
                'latitude'     => $request->latitude,
                'longitude'    => $request->longitude,
                'radius_meter' => $request->radius_meter,
                'status'       => 'Aktif',
            ]);

            return redirect()->route('absensi.pengaturan.index')
                ->with('success', 'Lokasi batas absensi berhasil ditambahkan.')
                ->with('action', 'save');
        } catch (\Exception $e) {
            return redirect()->route('absensi.pengaturan.index')
                ->with('error', 'Gagal menambahkan lokasi: ' . $e->getMessage());
        }
    }

    /**
     * Perbarui data lokasi absensi.
     */
    public function update(Request $request, $id)
    {
        $lokasi = PengaturanAbsensi::findOrFail($id);

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

        try {
            $lokasi->update([
                'nama_lokasi'  => $request->nama_lokasi,
                'latitude'     => $request->latitude,
                'longitude'    => $request->longitude,
                'radius_meter' => $request->radius_meter,
            ]);

            return redirect()->route('absensi.pengaturan.index')
                ->with('success', 'Lokasi batas absensi berhasil diperbarui.')
                ->with('action', 'save');
        } catch (\Exception $e) {
            return redirect()->route('absensi.pengaturan.index')
                ->with('error', 'Gagal memperbarui lokasi: ' . $e->getMessage());
        }
    }

    /**
     * Hapus lokasi absensi.
     */
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
}
