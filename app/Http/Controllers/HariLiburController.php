<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HariLibur;
use App\Models\TahunAkademik;
use Carbon\Carbon;

class HariLiburController extends Controller
{
    // daftar hari libur
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
    // akhir daftar hari libur

    // form tambah hari libur
    public function create()
    {
        $hariLiburList     = HariLibur::with('tahunAkademik')->orderBy('tanggal_mulai', 'desc')->get();
        $tahunAkademikList = TahunAkademik::orderBy('nama_tahun', 'desc')->get();
        return view('hari-libur.create', compact('hariLiburList', 'tahunAkademikList'));
    }
    // akhir form tambah hari libur

    // simpan hari libur
    public function store(Request $request)
    {
        // validasi input
        $request->validate([
            'id_tahun_akademik' => 'required|exists:tahun_akademik,id_tahun_akademik',
            'keterangan'        => 'required|string|max:255',
            'tipe'              => 'required|in:Nasional,Sekolah,Khusus',
            'tanggal_mulai'     => 'required|date',
            'tanggal_selesai'   => 'required|date|after_or_equal:tanggal_mulai',
        ]);
        // akhir validasi input

        $tahun = TahunAkademik::find($request->id_tahun_akademik);
        if ($tahun && !$tahun->is_aktif) {
            return redirect()->route('hari-libur.gagal')
                ->with('error', 'Tahun akademik ini belum aktif. Aktifkan tahun akademik terlebih dahulu melalui menu Tahun Akademik.');
        }

        try {
            // cek tanggal bertabrakan
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
            // akhir cek tanggal bertabrakan

            // simpan data ke database
            HariLibur::create([
                'id_tahun_akademik' => $request->id_tahun_akademik,
                'keterangan'        => $request->keterangan,
                'tipe'              => $request->tipe,
                'tanggal_mulai'     => $request->tanggal_mulai,
                'tanggal_selesai'   => $request->tanggal_selesai,
                'status'            => 'Aktif',
            ]);
            // akhir simpan data ke database

            return redirect()->route('hari-libur.berhasil');
        } catch (\Exception $e) {
            return redirect()->route('hari-libur.gagal')->with('error', $e->getMessage());
        }
    }
    // akhir simpan hari libur

    // form edit hari libur
    public function edit($id)
    {
        $hariLibur         = HariLibur::findOrFail($id);
        $hariLiburList     = HariLibur::with('tahunAkademik')->orderBy('tanggal_mulai', 'desc')->get();
        $tahunAkademikList = TahunAkademik::orderBy('nama_tahun', 'desc')->get();
        return view('hari-libur.edit', compact('hariLibur', 'hariLiburList', 'tahunAkademikList'));
    }
    // akhir form edit hari libur

    // perbarui hari libur
    public function update(Request $request, $id)
    {
        // validasi input
        $request->validate([
            'id_tahun_akademik' => 'required|exists:tahun_akademik,id_tahun_akademik',
            'keterangan'        => 'required|string|max:255',
            'tipe'              => 'required|in:Nasional,Sekolah,Khusus',
            'tanggal_mulai'     => 'required|date',
            'tanggal_selesai'   => 'required|date|after_or_equal:tanggal_mulai',
        ]);
        // akhir validasi input

        $tahun = TahunAkademik::find($request->id_tahun_akademik);
        if ($tahun && !$tahun->is_aktif) {
            return redirect()->route('hari-libur.gagal')
                ->with('error', 'Tahun akademik ini belum aktif. Aktifkan tahun akademik terlebih dahulu melalui menu Tahun Akademik.');
        }

        try {
            $hariLibur = HariLibur::findOrFail($id);

            // cek data ganda
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
            // akhir cek data ganda

            // perbarui ke database
            $hariLibur->update([
                'id_tahun_akademik' => $request->id_tahun_akademik,
                'keterangan'        => $request->keterangan,
                'tipe'              => $request->tipe,
                'tanggal_mulai'     => $request->tanggal_mulai,
                'tanggal_selesai'   => $request->tanggal_selesai,
            ]);
            // akhir perbarui ke database

            return redirect()->route('hari-libur.berhasil');
        } catch (\Exception $e) {
            return redirect()->route('hari-libur.gagal')->with('error', $e->getMessage());
        }
    }
    // akhir perbarui hari libur

    // tampilan berhasil
    public function berhasil(Request $request)
    {
        $hariLiburList = HariLibur::with('tahunAkademik')->orderBy('tanggal_mulai', 'desc')->get();
        $isDelete = $request->query('action') === 'delete' || session('action') === 'delete';
        return view('hari-libur.berhasil', compact('hariLiburList', 'isDelete'));
    }
    // akhir tampilan berhasil

    // tampilan gagal
    public function gagal(Request $request)
    {
        $hariLiburList = HariLibur::with('tahunAkademik')->orderBy('tanggal_mulai', 'desc')->get();
        return view('hari-libur.gagal', compact('hariLiburList'));
    }
    // akhir tampilan gagal

    // hapus hari libur
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
    // akhir hapus hari libur

    // pengecekan hari libur hari ini
    public static function isTodayHoliday(): bool
    {
        $today = Carbon::today()->toDateString();
        return HariLibur::where('status', 'Aktif')
            ->where('tanggal_mulai', '<=', $today)
            ->where('tanggal_selesai', '>=', $today)
            ->exists();
    }
    // akhir pengecekan hari libur hari ini
}
