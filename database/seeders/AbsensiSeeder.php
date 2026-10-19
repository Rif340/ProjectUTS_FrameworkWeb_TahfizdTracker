<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Guru;
use App\Models\Admin;
use App\Models\Murid;
use App\Models\Kelas;
use App\Models\AbsensiGuru;
use App\Models\AbsensiAdmin;
use App\Models\AbsensiMurid;
use Carbon\Carbon;

class AbsensiSeeder extends Seeder
{
    public function run(): void
    {
        $guru = Guru::first();
        $admin = Admin::first();
        $murid = Murid::first();

        // Pastikan ada kelas Tahfidz 3A sesuai Figma
        $kelas = Kelas::firstOrCreate(
            ['nama_kelas' => 'Tahfidz 3A'],
            [
                'id_guru' => $guru ? $guru->id_guru : 1,
                'id_tahun_akademik' => 1,
                'hari' => 'Selasa',
                'jam_mulai' => '07:00:00',
                'jam_selesai' => '15:30:00',
                'status' => 'Aktif',
            ]
        );

        if ($guru) {
            // Data sample sesuai Figma Node 638:4162
            $sampleData = [
                [
                    'id_guru' => $guru->id_guru,
                    'id_kelas' => $kelas->id_kelas,
                    'tanggal' => '2026-10-06',
                    'waktu_masuk' => '06:45:20',
                    'waktu_pulang' => '15:30:10',
                    'status' => 'tepat waktu',
                ],
                [
                    'id_guru' => $guru->id_guru,
                    'id_kelas' => $kelas->id_kelas,
                    'tanggal' => '2026-10-05',
                    'waktu_masuk' => '07:15:10',
                    'waktu_pulang' => '15:30:10',
                    'status' => 'terlambat',
                ],
                [
                    'id_guru' => $guru->id_guru,
                    'id_kelas' => $kelas->id_kelas,
                    'tanggal' => '2026-10-04',
                    'waktu_masuk' => null,
                    'waktu_pulang' => null,
                    'status' => 'tidak hadir',
                ],
                [
                    'id_guru' => $guru->id_guru,
                    'id_kelas' => $kelas->id_kelas,
                    'tanggal' => '2026-10-03',
                    'waktu_masuk' => '06:50:00',
                    'waktu_pulang' => '15:30:00',
                    'status' => 'tepat waktu',
                ],
                [
                    'id_guru' => $guru->id_guru,
                    'id_kelas' => $kelas->id_kelas,
                    'tanggal' => '2026-10-02',
                    'waktu_masuk' => '06:48:15',
                    'waktu_pulang' => '15:30:00',
                    'status' => 'tepat waktu',
                ],
            ];

            foreach ($sampleData as $data) {
                AbsensiGuru::updateOrCreate(
                    ['id_guru' => $data['id_guru'], 'tanggal' => $data['tanggal']],
                    $data
                );
            }
        }
    }
}
