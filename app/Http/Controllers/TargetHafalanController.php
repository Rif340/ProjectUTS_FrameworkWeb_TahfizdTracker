<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TargetHafalan;
use App\Models\Surat;
use App\Models\Kelas;
use App\Models\SetoranHafalan;
use App\Models\Murid;

class TargetHafalanController extends Controller
{
    // ambil tahun akademik aktif
    private function getActiveTahunAkademik(): ?\App\Models\TahunAkademik
    {
        $activeYearId = session('active_tahun_akademik_id');
        if ($activeYearId) {
            $tahun = \App\Models\TahunAkademik::find($activeYearId);
            if ($tahun) {
                return $tahun;
            }
        }

        return \App\Models\TahunAkademik::where('is_aktif', true)->first() ?? \App\Models\TahunAkademik::orderBy('tanggal_mulai', 'desc')->first();
    }
    // akhir ambil tahun akademik aktif

    // data tampilan target hafalan
    private function getTargetViewData(Request $request)
    {
        $perPage     = (int) $request->get('per_page', 10);
        $searchSurat = $request->get('surat', '');
        $searchKelas = $request->get('kelas', '');

        $activeTahun = $this->getActiveTahunAkademik();
        $activeYearId = $activeTahun ? $activeTahun->id_tahun_akademik : null;

        $kelasList = Kelas::when($activeYearId, fn($q) => $q->where('id_tahun_akademik', $activeYearId))
            ->orderBy('nama_kelas')
            ->get();
        $kelasIds = $kelasList->pluck('id_kelas')->toArray();
        $suratList = Surat::orderBy('id_surat')->get();

        $query = TargetHafalan::with(['surat', 'kelas'])->orderBy('id_target', 'desc');

        if ($activeYearId) {
            $query->whereIn('id_kelas', $kelasIds);
        }

        if ($searchSurat) {
            $query->where('id_surat', $searchSurat);
        }

        if ($searchKelas) {
            $query->where('id_kelas', $searchKelas);
        }

        $targetList = $query->paginate($perPage)->appends($request->except('page'));

        return compact('targetList', 'kelasList', 'suratList', 'perPage', 'searchSurat', 'searchKelas');
    }
    // akhir data tampilan target hafalan

    // daftar target hafalan
    public function index(Request $request)
    {
        return view('tahfizd.target.index', $this->getTargetViewData($request));
    }
    // akhir daftar target hafalan

    // simpan target hafalan
    public function store(Request $request)
    {
        // validasi input
        $request->validate([
            'id_surat' => 'required|exists:surat,id_surat',
            'id_kelas' => 'required|exists:kelas,id_kelas',
        ], [
            'id_surat.required' => 'Pilih surat terlebih dahulu.',
            'id_kelas.required' => 'Pilih kelas terlebih dahulu.',
        ]);
        // akhir validasi input

        try {
            $kelas = Kelas::with('tahunAkademik')->findOrFail($request->id_kelas);
            if ($kelas->tahunAkademik && !$kelas->tahunAkademik->is_aktif) {
                return redirect()->route('tahfizd.target.gagal')
                    ->with('error', 'Tahun akademik untuk kelas ini belum aktif. Aktifkan tahun akademik terlebih dahulu melalui menu Tahun Akademik.');
            }

            // cek target ganda
            $exists = TargetHafalan::where('id_surat', $request->id_surat)
                ->where('id_kelas', $request->id_kelas)
                ->exists();
            if ($exists) {
                return redirect()->route('tahfizd.target.gagal')
                    ->with('error', 'Target hafalan untuk surat ini di kelas tersebut sudah ada.');
            }
            // akhir cek target ganda

            // simpan ke database
            TargetHafalan::create([
                'id_surat' => $request->id_surat,
                'id_kelas' => $request->id_kelas,
                'status'   => 'aktif',
            ]);
            // akhir simpan ke database

            return redirect()->route('tahfizd.target.berhasil');
        } catch (\Exception $e) {
            return redirect()->route('tahfizd.target.gagal')->with('error', $e->getMessage());
        }
    }
    // akhir simpan target hafalan

    // hapus target hafalan
    public function destroy($id)
    {
        try {
            $target = TargetHafalan::with('kelas.tahunAkademik')->findOrFail($id);
            if ($target->kelas && $target->kelas->tahunAkademik && !$target->kelas->tahunAkademik->is_aktif) {
                return redirect()->route('tahfizd.target.gagal')
                    ->with('error', 'Tahun akademik untuk target hafalan ini belum aktif. Aktifkan tahun akademik terlebih dahulu melalui menu Tahun Akademik.');
            }
            $target->delete();
            return redirect()->route('tahfizd.target.berhasil', ['action' => 'delete']);
        } catch (\Exception $e) {
            return redirect()->route('tahfizd.target.gagal')->with('error', 'Gagal menghapus target hafalan.');
        }
    }
    // akhir hapus target hafalan

    // tampilan berhasil
    public function berhasil(Request $request)
    {
        $isDelete = $request->query('action') === 'delete';
        $data = $this->getTargetViewData($request);
        $data['isDelete'] = $isDelete;
        return view('tahfizd.target.berhasil', $data);
    }
    // akhir tampilan berhasil

    // tampilan gagal
    public function gagal(Request $request)
    {
        return view('tahfizd.target.gagal', $this->getTargetViewData($request));
    }
    // akhir tampilan gagal

    // cetak rekap per kelas
    public function cetakKelas($kelasId)
    {
        $kelas      = Kelas::with(['guru.user', 'tahunAkademik'])->findOrFail($kelasId);
        $muridList  = Murid::with('user')
            ->where('id_kelas', $kelasId)
            ->join('users', 'murid.id_user', '=', 'users.id_user')
            ->select('murid.*')
            ->orderBy('users.nama', 'asc')
            ->get();

        $targetIds = TargetHafalan::where('id_kelas', $kelasId)->pluck('id_surat')->toArray();
        if (!empty($targetIds)) {
            $suratList = Surat::whereIn('id_surat', $targetIds)->orderBy('id_surat', 'asc')->get();
        } else {
            $suratList = Surat::orderBy('id_surat', 'asc')->get();
        }

        $rows = [];
        $totalPerSurat = [];
        foreach ($suratList as $s) {
            $totalPerSurat[$s->id_surat] = 0;
        }
        $grandTotalSelesai = 0;
        $jumlahTargetTuntas = 0;

        foreach ($muridList as $m) {
            $setoranMurid = SetoranHafalan::where('id_murid', $m->id_murid)->get();
            $suratStatus = [];
            $totalSuratSelesai = 0;
            $allTargetPassed = !empty($targetIds);

            foreach ($suratList as $s) {
                $setoranSurat = $setoranMurid->where('id_surat', $s->id_surat);
                $lulusSetoran = $setoranSurat->filter(function($row) {
                    return in_array(strtolower(trim($row->status ?? '')), ['lancar', 'lulus', 'selesai', 'tuntas']);
                });
                $totalAyatSetor = $lulusSetoran->sum(fn($x) => max(0, $x->ayat_selesai - $x->ayat_mulai + 1));
                $isLulus = ($s->jumlah_ayat > 0 && $totalAyatSetor >= $s->jumlah_ayat);

                $hasMengulang = $setoranSurat->filter(function($row) {
                    return in_array(strtolower(trim($row->status ?? '')), ['mengulang', 'perlu perbaikan']);
                })->count() > 0;

                if ($isLulus) {
                    $suratStatus[$s->id_surat] = ['symbol' => '✓', 'status' => 'lulus'];
                    $totalSuratSelesai++;
                    $totalPerSurat[$s->id_surat]++;
                } elseif ($hasMengulang && $totalAyatSetor == 0) {
                    $suratStatus[$s->id_surat] = ['symbol' => 'M', 'status' => 'mengulang'];
                } elseif ($totalAyatSetor > 0) {
                    $suratStatus[$s->id_surat] = ['symbol' => (string)min($s->jumlah_ayat, $totalAyatSetor), 'status' => 'progress'];
                } else {
                    $suratStatus[$s->id_surat] = ['symbol' => '—', 'status' => 'belum'];
                }

                if (in_array($s->id_surat, $targetIds) && !$isLulus) {
                    $allTargetPassed = false;
                }
            }

            if ($allTargetPassed) {
                $jumlahTargetTuntas++;
            }

            $grandTotalSelesai += $totalSuratSelesai;
            $rows[] = [
                'murid' => $m,
                'suratStatus' => $suratStatus,
                'totalSuratSelesai' => $totalSuratSelesai,
            ];
        }

        $murid = $muridList->first();

        return view('siswa.cetak', compact(
            'kelas',
            'murid',
            'suratList',
            'rows',
            'totalPerSurat',
            'grandTotalSelesai',
            'jumlahTargetTuntas'
        ));
    }
    // akhir cetak rekap per kelas
}
