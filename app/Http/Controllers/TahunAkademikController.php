<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TahunAkademik;

class TahunAkademikController extends Controller
{
    /**
     * Tampilkan daftar tahun akademik (Figma Node 618:1198).
     */
    public function index()
    {
        $tahunAkademikList = TahunAkademik::orderBy('tanggal_mulai', 'desc')->get();
        return view('tahun-akademik.index', compact('tahunAkademikList'));
    }

    /**
     * Tampilkan form tambah tahun akademik (Figma Node 625:3214).
     */
    public function create()
    {
        $tahunAkademikList = TahunAkademik::orderBy('tanggal_mulai', 'desc')->get();
        return view('tahun-akademik.create', compact('tahunAkademikList'));
    }

    /**
     * Simpan tahun akademik baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_tahun'      => 'required|string|max:50',
            'semester'        => 'required|in:Ganjil,Genap',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'is_aktif'        => 'nullable|in:0,1',
        ]);

        try {
            // Cek apakah ada data dengan nama_tahun & semester yang sama,
            // ATAU tanggal yang overlap (bentrok)
            $exists = TahunAkademik::where(function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('nama_tahun', $request->nama_tahun)
                        ->where('semester', $request->semester);
                })->orWhere(function ($sub) use ($request) {
                    $sub->where('tanggal_mulai', '<=', $request->tanggal_selesai)
                        ->where('tanggal_selesai', '>=', $request->tanggal_mulai);
                });
            })->exists();

            if ($exists) {
                return redirect()->route('tahun-akademik.gagal')
                    ->with('error', 'Tahun akademik ' . $request->nama_tahun . ' (' . $request->semester . ') sudah terdaftar atau rentang tanggal bertabrakan dengan data yang ada.');
            }

            $isAktif = $request->has('is_aktif') ? $request->boolean('is_aktif') : false;
            if ($isAktif) {
                // Nonaktifkan tahun akademik lain agar tidak bentrok
                TahunAkademik::query()->update(['is_aktif' => false]);
            } elseif (!TahunAkademik::where('is_aktif', true)->exists()) {
                // Jika belum ada tahun akademik aktif sama sekali, jadikan tahun pertama aktif
                $isAktif = true;
            }

            $tahun = TahunAkademik::create([
                'nama_tahun'      => $request->nama_tahun,
                'semester'        => $request->semester,
                'tanggal_mulai'   => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'is_aktif'        => $isAktif,
            ]);

            if ($isAktif) {
                session(['active_tahun_akademik_id' => $tahun->id_tahun_akademik]);
            }

            return redirect()->route('tahun-akademik.berhasil');
        } catch (\Exception $e) {
            return redirect()->route('tahun-akademik.gagal')->with('error', $e->getMessage());
        }
    }

    /**
     * Tampilkan form edit tahun akademik (Figma Node 625:4167).
     */
    public function edit($id)
    {
        $tahunAkademik     = TahunAkademik::findOrFail($id);
        $tahunAkademikList = TahunAkademik::orderBy('tanggal_mulai', 'desc')->get();
        return view('tahun-akademik.edit', compact('tahunAkademik', 'tahunAkademikList'));
    }

    /**
     * Perbarui data tahun akademik.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_tahun'      => 'required|string|max:50',
            'semester'        => 'required|in:Ganjil,Genap',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'is_aktif'        => 'nullable|in:0,1',
        ]);

        try {
            $tahunAkademik = TahunAkademik::findOrFail($id);

            // Cek apakah ada record lain dengan nama_tahun & semester yang sama,
            // ATAU tanggal yang overlap (bentrok), kecuali record itu sendiri
            $exists = TahunAkademik::where('id_tahun_akademik', '!=', $id)
                ->where(function ($q) use ($request) {
                    $q->where(function ($sub) use ($request) {
                        $sub->where('nama_tahun', $request->nama_tahun)
                            ->where('semester', $request->semester);
                    })->orWhere(function ($sub) use ($request) {
                        $sub->where('tanggal_mulai', '<=', $request->tanggal_selesai)
                            ->where('tanggal_selesai', '>=', $request->tanggal_mulai);
                    });
                })->exists();

            if ($exists) {
                return redirect()->route('tahun-akademik.gagal')
                    ->with('error', 'Tahun akademik ' . $request->nama_tahun . ' (' . $request->semester . ') sudah terdaftar atau rentang tanggal bertabrakan dengan data yang ada.');
            }

            $isAktif = $request->has('is_aktif') ? $request->boolean('is_aktif') : $tahunAkademik->is_aktif;
            if ($isAktif) {
                // Nonaktifkan semua tahun akademik lain agar tidak bentrok
                TahunAkademik::where('id_tahun_akademik', '!=', $id)->update(['is_aktif' => false]);
                session(['active_tahun_akademik_id' => $id]);
            }

            $tahunAkademik->update([
                'nama_tahun'      => $request->nama_tahun,
                'semester'        => $request->semester,
                'tanggal_mulai'   => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'is_aktif'        => $isAktif,
            ]);

            return redirect()->route('tahun-akademik.berhasil');
        } catch (\Exception $e) {
            return redirect()->route('tahun-akademik.gagal')->with('error', $e->getMessage());
        }
    }

    /**
     * Hapus tahun akademik.
     */
    public function destroy($id)
    {
        try {
            $tahunAkademik = TahunAkademik::findOrFail($id);
            if ($tahunAkademik->is_aktif) {
                return redirect()->route('tahun-akademik.gagal')->with('error', 'Tahun akademik yang sedang aktif tidak dapat dihapus. Silakan aktifkan tahun akademik lain terlebih dahulu.');
            }
            $tahunAkademik->delete();
            return redirect()->route('tahun-akademik.berhasil', ['action' => 'delete']);
        } catch (\Exception $e) {
            return redirect()->route('tahun-akademik.gagal')->with('error', 'Gagal menghapus data tahun akademik: ' . $e->getMessage());
        }
    }

    /**
     * Halaman alert berhasil.
     */
    public function berhasil(Request $request)
    {
        $tahunAkademikList = TahunAkademik::orderBy('tanggal_mulai', 'desc')->get();
        $isDelete = $request->query('action') === 'delete' || session('action') === 'delete';
        return view('tahun-akademik.berhasil', compact('tahunAkademikList', 'isDelete'));
    }

    /**
     * Halaman alert gagal.
     */
    public function gagal(Request $request)
    {
        $tahunAkademikList = TahunAkademik::orderBy('tanggal_mulai', 'desc')->get();
        return view('tahun-akademik.gagal', compact('tahunAkademikList'));
    }

    /**
     * Set tahun akademik aktif dari dropdown navbar (hanya session, tidak mengubah status aktif DB).
     */
    public function setActiveYear(Request $request, $id)
    {
        try {
            $tahun = TahunAkademik::findOrFail($id);
            session(['active_tahun_akademik_id' => $tahun->id_tahun_akademik]);

            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back();
        }
    }

    /**
     * Jadikan tahun akademik sebagai tahun aktif resmi di database (Hanya Admin).
     */
    public function aktifkan($id)
    {
        try {
            $tahun = TahunAkademik::findOrFail($id);
            TahunAkademik::query()->update(['is_aktif' => false]);
            $tahun->update(['is_aktif' => true]);
            session(['active_tahun_akademik_id' => $tahun->id_tahun_akademik]);

            return redirect()->route('tahun-akademik.index')->with('success', 'Tahun akademik ' . $tahun->nama_tahun . ' (' . $tahun->semester . ') berhasil diaktifkan.');
        } catch (\Exception $e) {
            return redirect()->route('tahun-akademik.gagal')->with('error', 'Gagal mengaktifkan tahun akademik: ' . $e->getMessage());
        }
    }
}
