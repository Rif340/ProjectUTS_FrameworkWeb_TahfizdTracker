<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SuratSeeder extends Seeder
{
    public function run(): void
    {
        $suratList = [
            ['id_surat' => 78, 'nama_surat' => "An-Naba'", 'jumlah_ayat' => 40, 'juz' => 30],
            ['id_surat' => 79, 'nama_surat' => "An-Nazi'at", 'jumlah_ayat' => 46, 'juz' => 30],
            ['id_surat' => 80, 'nama_surat' => "'Abasa", 'jumlah_ayat' => 42, 'juz' => 30],
            ['id_surat' => 81, 'nama_surat' => "At-Takwir", 'jumlah_ayat' => 29, 'juz' => 30],
            ['id_surat' => 82, 'nama_surat' => "Al-Infitar", 'jumlah_ayat' => 19, 'juz' => 30],
            ['id_surat' => 83, 'nama_surat' => "Al-Mutaffifin", 'jumlah_ayat' => 36, 'juz' => 30],
            ['id_surat' => 84, 'nama_surat' => "Al-Insyiqaq", 'jumlah_ayat' => 25, 'juz' => 30],
            ['id_surat' => 85, 'nama_surat' => "Al-Buruj", 'jumlah_ayat' => 22, 'juz' => 30],
            ['id_surat' => 86, 'nama_surat' => "At-Tariq", 'jumlah_ayat' => 17, 'juz' => 30],
            ['id_surat' => 87, 'nama_surat' => "Al-A'la", 'jumlah_ayat' => 19, 'juz' => 30],
            ['id_surat' => 88, 'nama_surat' => "Al-Ghasyiyah", 'jumlah_ayat' => 26, 'juz' => 30],
            ['id_surat' => 89, 'nama_surat' => "Al-Fajr", 'jumlah_ayat' => 30, 'juz' => 30],
            ['id_surat' => 90, 'nama_surat' => "Al-Balad", 'jumlah_ayat' => 20, 'juz' => 30],
            ['id_surat' => 91, 'nama_surat' => "Asy-Syams", 'jumlah_ayat' => 15, 'juz' => 30],
            ['id_surat' => 92, 'nama_surat' => "Al-Lail", 'jumlah_ayat' => 21, 'juz' => 30],
            ['id_surat' => 93, 'nama_surat' => "Ad-Duha", 'jumlah_ayat' => 11, 'juz' => 30],
            ['id_surat' => 94, 'nama_surat' => "Asy-Syarh", 'jumlah_ayat' => 8, 'juz' => 30],
            ['id_surat' => 95, 'nama_surat' => "At-Tin", 'jumlah_ayat' => 8, 'juz' => 30],
            ['id_surat' => 96, 'nama_surat' => "Al-'Alaq", 'jumlah_ayat' => 19, 'juz' => 30],
            ['id_surat' => 97, 'nama_surat' => "Al-Qadr", 'jumlah_ayat' => 5, 'juz' => 30],
            ['id_surat' => 98, 'nama_surat' => "Al-Bayyinah", 'jumlah_ayat' => 8, 'juz' => 30],
            ['id_surat' => 99, 'nama_surat' => "Az-Zalzalah", 'jumlah_ayat' => 8, 'juz' => 30],
            ['id_surat' => 100, 'nama_surat' => "Al-'Adiyat", 'jumlah_ayat' => 11, 'juz' => 30],
            ['id_surat' => 101, 'nama_surat' => "Al-Qari'ah", 'jumlah_ayat' => 11, 'juz' => 30],
            ['id_surat' => 102, 'nama_surat' => "At-Takasur", 'jumlah_ayat' => 8, 'juz' => 30],
            ['id_surat' => 103, 'nama_surat' => "Al-'Asr", 'jumlah_ayat' => 3, 'juz' => 30],
            ['id_surat' => 104, 'nama_surat' => "Al-Humazah", 'jumlah_ayat' => 9, 'juz' => 30],
            ['id_surat' => 105, 'nama_surat' => "Al-Fil", 'jumlah_ayat' => 5, 'juz' => 30],
            ['id_surat' => 106, 'nama_surat' => "Quraisy", 'jumlah_ayat' => 4, 'juz' => 30],
            ['id_surat' => 107, 'nama_surat' => "Al-Ma'un", 'jumlah_ayat' => 7, 'juz' => 30],
            ['id_surat' => 108, 'nama_surat' => "Al-Kausar", 'jumlah_ayat' => 3, 'juz' => 30],
            ['id_surat' => 109, 'nama_surat' => "Al-Kafirun", 'jumlah_ayat' => 6, 'juz' => 30],
            ['id_surat' => 110, 'nama_surat' => "An-Nasr", 'jumlah_ayat' => 3, 'juz' => 30],
            ['id_surat' => 111, 'nama_surat' => "Al-Lahab", 'jumlah_ayat' => 5, 'juz' => 30],
            ['id_surat' => 112, 'nama_surat' => "Al-Ikhlas", 'jumlah_ayat' => 4, 'juz' => 30],
            ['id_surat' => 113, 'nama_surat' => "Al-Falaq", 'jumlah_ayat' => 5, 'juz' => 30],
            ['id_surat' => 114, 'nama_surat' => "An-Nas", 'jumlah_ayat' => 6, 'juz' => 30],
        ];

        foreach ($suratList as $item) {
            DB::table('surat')->updateOrInsert(
                ['id_surat' => $item['id_surat']],
                [
                    'nama_surat' => $item['nama_surat'],
                    'jumlah_ayat' => $item['jumlah_ayat'],
                    'juz' => $item['juz'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
