<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Admin;
use App\Models\Guru;
use App\Models\Murid;
use App\Models\TahunAkademik;
use App\Models\Kelas;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tahun Akademik default
        $tahun = TahunAkademik::firstOrCreate(
            ['nama_tahun' => '2026/2027'],
            [
                'tanggal_mulai' => '2026-07-15',
                'tanggal_selesai' => '2026-12-20',
                'is_aktif' => true,
            ]
        );

        // 2. Akun Guru: Belinda Blanche, S.Kom
        $userGuru = User::updateOrCreate(
            ['email' => 'belindablache@gmail.com'],
            [
                'nama' => 'Belinda Blanche, S.Kom',
                'password' => Hash::make('guru123'),
                'role' => 'Guru',
                'status' => 'Aktif',
            ]
        );

        $guru = Guru::updateOrCreate(
            ['id_user' => $userGuru->id_user],
            [
                'nip' => '1234567890',
                'qr_code' => '1234567890',
                'no_hp' => '081234567891',
                'status' => 'Aktif',
            ]
        );

        // 3. Akun Admin: Ino
        $userAdmin = User::updateOrCreate(
            ['email' => 'ino@gmail.com'],
            [
                'nama' => 'Ino',
                'password' => Hash::make('admin123'),
                'role' => 'Admin',
                'status' => 'Aktif',
            ]
        );

        Admin::updateOrCreate(
            ['id_user' => $userAdmin->id_user],
            [
                'nip' => '1234567890',
                'qr_code' => '1234567890',
                'no_hp' => '081234567890',
                'status' => 'Aktif',
            ]
        );

        // 4. Kelas 6A yang diajar oleh Belinda Blanche
        $kelas = Kelas::firstOrCreate(
            ['nama_kelas' => '6A', 'id_tahun_akademik' => $tahun->id_tahun_akademik],
            [
                'id_guru' => $guru->id_guru,
                'hari' => 'Senin',
                'jam_mulai' => '07:30:00',
                'jam_selesai' => '09:00:00',
                'status' => 'Aktif',
            ]
        );

        // 5. Akun Murid: Bilal Hakim
        $userMurid = User::updateOrCreate(
            ['email' => 'bilalhakim@gmail.com'],
            [
                'nama' => 'Bilal Hakim',
                'password' => Hash::make('murid123'),
                'role' => 'Murid',
                'status' => 'Aktif',
            ]
        );

        Murid::updateOrCreate(
            ['id_user' => $userMurid->id_user],
            [
                'id_kelas' => $kelas->id_kelas,
                'nisn' => '1234567890',
                'qr_code' => '1234567890',
                'no_hp' => '081234567892',
                'status' => 'Aktif',
            ]
        );

        \App\Services\QRCodeService::syncAll();
    }
}
