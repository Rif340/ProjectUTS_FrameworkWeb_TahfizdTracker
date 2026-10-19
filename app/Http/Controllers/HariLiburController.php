<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HariLibur;
use App\Models\TahunAkademik;
use Carbon\Carbon;

class HariLiburController extends Controller
{
    /**
     * Tampilkan daftar hari libur.
     */
    public function index()
    {
        $activeYearId = session('active_tahun_akademik_id');
        $query = HariLibur::with('tahunAkademik')->orderBy('tanggal_mulai', 'desc');
        if ($activeYearId) {
            $query->where('id_tahun_akademik', $activeYearId);
        }
        $hariLiburList = $query->get();
        return view('hari-libur.index', compact('hariLiburList'));
    }

    /**
     * Tampilkan form tambah hari libur.
     */
    public function create()
    {
        $hariLiburList     = HariLibur::with('tahunAkademik')->orderBy('tanggal_mulai', 'desc')->get();
        $tahunAkademikList = TahunAkademik::orderBy('nama_tahun', 'desc')->get();
        return view('hari-libur.create', compact('hariLiburList', 'tahunAkademikList'));
    }

    /**
     * Simpan hari libur baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_tahun_akademik' => 'required|exists:tahun_akademik,id_tahun_akademik',
            'keterangan'        => 'required|string|max:255',
            'tipe'              => 'required|in:Nasional,Sekolah,Khusus',
            'tanggal_mulai'     => 'required|date',
            'tanggal_selesai'   => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        $tahun = TahunAkademik::find($request->id_tahun_akademik);
        if ($tahun && !$tahun->is_aktif) {
            return redirect()->route('hari-libur.gagal')
                ->with('error', 'Tahun akademik ini belum aktif. Aktifkan tahun akademik terlebih dahulu melalui menu Tahun Akademik.');
        }

        try {
            // Cek apakah ada data yang overlapping atau sama persis
            $exists = HariLibur::where('id_tahun_akademik', $request->id_tahun_akademik)
                ->where(function ($q) use ($request) {
                    $q->where(function ($sub) use ($request) {
                        $sub->where('tanggal_mulai', '<=', $request->tanggal_selesai)
                            ->where('tanggal_selesai', '>=', $request->tanggal_mulai);
                    })
                    ->orWhere('keterangan', $request->keterangan);
                })
                ->exists();

            if ($exists) {
                return redirect()->route('hari-libur.gagal')->with('error', 'Data hari libur dengan tanggal bertabrakan atau keterangan yang sama sudah ada.');
            }

            HariLibur::create([
                'id_tahun_akademik' => $request->id_tahun_akademik,
                'keterangan'        => $request->keterangan,
                'tipe'              => $request->tipe,
                'tanggal_mulai'     => $request->tanggal_mulai,
                'tanggal_selesai'   => $request->tanggal_selesai,
                'status'            => 'Aktif',
            ]);

            return redirect()->route('hari-libur.berhasil');
        } catch (\Exception $e) {
            return redirect()->route('hari-libur.gagal')->with('error', $e->getMessage());
        }
    }

    /**
     * Tampilkan form edit hari libur (Figma Node 625:4377).
     */
    public function edit($id)
    {
        $hariLibur         = HariLibur::findOrFail($id);
        $hariLiburList     = HariLibur::with('tahunAkademik')->orderBy('tanggal_mulai', 'desc')->get();
        $tahunAkademikList = TahunAkademik::orderBy('nama_tahun', 'desc')->get();
        return view('hari-libur.edit', compact('hariLibur', 'hariLiburList', 'tahunAkademikList'));
    }

    /**
     * Perbarui data hari libur.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'id_tahun_akademik' => 'required|exists:tahun_akademik,id_tahun_akademik',
            'keterangan'        => 'required|string|max:255',
            'tipe'              => 'required|in:Nasional,Sekolah,Khusus',
            'tanggal_mulai'     => 'required|date',
            'tanggal_selesai'   => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        $tahun = TahunAkademik::find($request->id_tahun_akademik);
        if ($tahun && !$tahun->is_aktif) {
            return redirect()->route('hari-libur.gagal')
                ->with('error', 'Tahun akademik ini belum aktif. Aktifkan tahun akademik terlebih dahulu melalui menu Tahun Akademik.');
        }

        try {
            $hariLibur = HariLibur::findOrFail($id);

            // Cek apakah ada data yang overlapping atau sama persis selain record ini
            $exists = HariLibur::where('id_tahun_akademik', $request->id_tahun_akademik)
                ->where('id_hari_libur', '!=', $id)
                ->where(function ($q) use ($request) {
                    $q->where(function ($sub) use ($request) {
                        $sub->where('tanggal_mulai', '<=', $request->tanggal_selesai)
                            ->where('tanggal_selesai', '>=', $request->tanggal_mulai);
                    })
                    ->orWhere('keterangan', $request->keterangan);
                })
                ->exists();

            if ($exists) {
                return redirect()->route('hari-libur.gagal')->with('error', 'Data hari libur dengan tanggal bertabrakan atau keterangan yang sama sudah ada.');
            }

            $hariLibur->update([
                'id_tahun_akademik' => $request->id_tahun_akademik,
                'keterangan'        => $request->keterangan,
                'tipe'              => $request->tipe,
                'tanggal_mulai'     => $request->tanggal_mulai,
                'tanggal_selesai'   => $request->tanggal_selesai,
            ]);

            return redirect()->route('hari-libur.berhasil');
        } catch (\Exception $e) {
            return redirect()->route('hari-libur.gagal')->with('error', $e->getMessage());
        }
    }

    /**
     * Halaman alert berhasil.
     */
    public function berhasil(Request $request)
    {
        $hariLiburList = HariLibur::with('tahunAkademik')->orderBy('tanggal_mulai', 'desc')->get();
        $isDelete = $request->query('action') === 'delete' || session('action') === 'delete';
        return view('hari-libur.berhasil', compact('hariLiburList', 'isDelete'));
    }

    /**
     * Halaman alert gagal.
     */
    public function gagal(Request $request)
    {
        $hariLiburList = HariLibur::with('tahunAkademik')->orderBy('tanggal_mulai', 'desc')->get();
        return view('hari-libur.gagal', compact('hariLiburList'));
    }

    /**
     * Hapus hari libur.
     */
    public function destroy($id)
    {
        try {
            $hariLibur = HariLibur::with('tahunAkademik')->findOrFail($id);
            if ($hariLibur->tahunAkademik && !$hariLibur->tahunAkademik->is_aktif) {
                return redirect()->route('hari-libur.gagal')
                    ->with('error', 'Tahun akademik ini belum aktif. Aktifkan tahun akademik terlebih dahulu melalui menu Tahun Akademik.');
            }

            $hariLibur->delete();
            return redirect()->route('hari-libur.berhasil', ['action' => 'delete']);
        } catch (\Exception $e) {
            return redirect()->route('hari-libur.gagal')->with('error', 'Gagal menghapus data hari libur.');
        }
    }

    /**
     * Cek apakah tanggal tertentu adalah hari libur.
     * Digunakan oleh AbsensiController.
     */
    public static function isTodayHoliday(): bool
    {
        $today = Carbon::today()->toDateString();
        return HariLibur::where('status', 'Aktif')
            ->where('tanggal_mulai', '<=', $today)
            ->where('tanggal_selesai', '>=', $today)
            ->exists();
    }
}
