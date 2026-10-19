<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HariKerja;

class HariKerjaSeeder extends Seeder
{
    public function run(): void
    {
        $days = [
            ['hari' => 'Senin', 'jam_masuk' => '07:00:00', 'jam_pulang' => '14:00:00', 'status' => 'Aktif'],
            ['hari' => 'Selasa', 'jam_masuk' => '07:00:00', 'jam_pulang' => '14:00:00', 'status' => 'Aktif'],
            ['hari' => 'Rabu', 'jam_masuk' => '07:00:00', 'jam_pulang' => '14:00:00', 'status' => 'Aktif'],
            ['hari' => 'Kamis', 'jam_masuk' => '07:00:00', 'jam_pulang' => '14:00:00', 'status' => 'Aktif'],
            ['hari' => 'Jumat', 'jam_masuk' => '07:00:00', 'jam_pulang' => '14:00:00', 'status' => 'Aktif'],
            ['hari' => 'Sabtu', 'jam_masuk' => '07:00:00', 'jam_pulang' => '14:00:00', 'status' => 'Nonaktif'],
            ['hari' => 'Minggu', 'jam_masuk' => '07:00:00', 'jam_pulang' => '14:00:00', 'status' => 'Nonaktif'],
        ];

        foreach ($days as $day) {
            HariKerja::updateOrCreate(['hari' => $day['hari']], $day);
        }
    }
}
