<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TahunAkademik;

class TahunAkademikController extends Controller
{
    // daftar tahun akademik
    public function index()
    {
        $tahunAkademikList = TahunAkademik::orderBy('tanggal_mulai', 'desc')->get();
        return view('tahun-akademik.index', compact('tahunAkademikList'));
    }
    // akhir daftar tahun akademik

    // form tambah tahun akademik
    public function create()
    {
        $tahunAkademikList = TahunAkademik::orderBy('tanggal_mulai', 'desc')->get();
        return view('tahun-akademik.create', compact('tahunAkademikList'));
    }
    // akhir form tambah tahun akademik

    // simpan tahun akademik
    public function store(Request $request)
    {
        // validasi input
        $request->validate([
            'nama_tahun'      => 'required|string|max:50',
            'semester'        => 'required|in:Ganjil,Genap',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'is_aktif'        => 'nullable|in:0,1',
        ]);
        // akhir validasi input

        try {
            // cek bentrok tanggal atau nama semester
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
            // akhir cek bentrok tanggal atau nama semester

            // pengaturan keaktifan tahun
            $isAktif = $request->has('is_aktif') ? $request->boolean('is_aktif') : false;
            if ($isAktif) {
                TahunAkademik::query()->update(['is_aktif' => false]);
            } elseif (!TahunAkademik::where('is_aktif', true)->exists()) {
                $isAktif = true;
            }
            // akhir pengaturan keaktifan tahun

            // simpan data ke database
            $tahun = TahunAkademik::create([
                'nama_tahun'      => $request->nama_tahun,
                'semester'        => $request->semester,
                'tanggal_mulai'   => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'is_aktif'        => $isAktif,
            ]);
            // akhir simpan data ke database

            if ($isAktif) {
                session(['active_tahun_akademik_id' => $tahun->id_tahun_akademik]);
            }

            return redirect()->route('tahun-akademik.berhasil');
        } catch (\Exception $e) {
            return redirect()->route('tahun-akademik.gagal')->with('error', $e->getMessage());
        }
    }
    // akhir simpan tahun akademik

    // form edit tahun akademik
    public function edit($id)
    {
        $tahunAkademik     = TahunAkademik::findOrFail($id);
        $tahunAkademikList = TahunAkademik::orderBy('tanggal_mulai', 'desc')->get();
        return view('tahun-akademik.edit', compact('tahunAkademik', 'tahunAkademikList'));
    }
    // akhir form edit tahun akademik

    // perbarui tahun akademik
    public function update(Request $request, $id)
    {
        // validasi input
        $request->validate([
            'nama_tahun'      => 'required|string|max:50',
            'semester'        => 'required|in:Ganjil,Genap',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'is_aktif'        => 'nullable|in:0,1',
        ]);
        // akhir validasi input

        try {
            $tahunAkademik = TahunAkademik::findOrFail($id);

            // cek bentrok tanggal selain data ini
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
            // akhir cek bentrok tanggal selain data ini

            // perbarui keaktifan
            $isAktif = $request->has('is_aktif') ? $request->boolean('is_aktif') : $tahunAkademik->is_aktif;
            if ($isAktif) {
                TahunAkademik::where('id_tahun_akademik', '!=', $id)->update(['is_aktif' => false]);
                session(['active_tahun_akademik_id' => $id]);
            }
            // akhir perbarui keaktifan

            // perbarui ke database
            $tahunAkademik->update([
                'nama_tahun'      => $request->nama_tahun,
                'semester'        => $request->semester,
                'tanggal_mulai'   => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'is_aktif'        => $isAktif,
            ]);
            // akhir perbarui ke database

            return redirect()->route('tahun-akademik.berhasil');
        } catch (\Exception $e) {
            return redirect()->route('tahun-akademik.gagal')->with('error', $e->getMessage());
        }
    }
    // akhir perbarui tahun akademik

    // hapus tahun akademik
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
    // akhir hapus tahun akademik

    // tampilan berhasil
    public function berhasil(Request $request)
    {
        $tahunAkademikList = TahunAkademik::orderBy('tanggal_mulai', 'desc')->get();
        $isDelete = $request->query('action') === 'delete' || session('action') === 'delete';
        return view('tahun-akademik.berhasil', compact('tahunAkademikList', 'isDelete'));
    }
    // akhir tampilan berhasil

    // tampilan gagal
    public function gagal(Request $request)
    {
        $tahunAkademikList = TahunAkademik::orderBy('tanggal_mulai', 'desc')->get();
        return view('tahun-akademik.gagal', compact('tahunAkademikList'));
    }
    // akhir tampilan gagal

    // atur tahun akademik aktif sesi
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
    // akhir atur tahun akademik aktif sesi

    // aktifkan tahun akademik resmi
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
    // akhir aktifkan tahun akademik resmi
}
