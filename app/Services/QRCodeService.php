<?php

namespace App\Services;

use App\Models\User;
use App\Models\Admin;
use App\Models\Guru;
use App\Models\Murid;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class QRCodeService
{
    /**
     * Dapatkan detail identifier pengguna (NIP untuk Admin/Guru, NISN untuk Murid).
     */
    public static function getUserIdentifier(User $user, &$detail = null)
    {
        $roleLower = strtolower($user->role);
        $identifier = null;

        if ($roleLower === 'admin') {
            $detail = $detail ?: Admin::where('id_user', $user->id_user)->first();
            $identifier = $detail->nip ?? null;
        } elseif ($roleLower === 'guru') {
            $detail = $detail ?: Guru::where('id_user', $user->id_user)->first();
            $identifier = $detail->nip ?? null;
        } elseif ($roleLower === 'murid') {
            $detail = $detail ?: Murid::where('id_user', $user->id_user)->first();
            $identifier = $detail->nisn ?? null;
        }

        // Jika NIP/NISN belum diisi, gunakan email atau ID pengguna
        if (empty($identifier)) {
            $identifier = (string) $user->id_user;
        }

        return (string) $identifier;
    }

    /**
     * Dapatkan atau buat QR Code untuk pengguna tertentu.
     * Mengembalikan informasi file dan URL di storage/app/public/qrcode.
     */
    public static function getOrGenerateUserQR(User $user, $detail = null)
    {
        $identifier = self::getUserIdentifier($user, $detail);

        // Pastikan kolom qr_code di database terisi dengan NIP/NISN (bukan format dummy lama)
        if ($detail && ($detail->qr_code !== $identifier)) {
            $detail->qr_code = $identifier;
            $detail->save();
        }

        // Format nama file: QR_Role_Nama_Identifier.png
        $safeName = Str::slug($user->nama, '_');
        $safeRole = ucfirst($user->role);
        $filename = "QR_{$safeRole}_{$safeName}_{$identifier}.png";

        $storageRelativePath = "qrcode/{$filename}";
        $fullPath = storage_path("app/public/{$storageRelativePath}");

        // Jika file belum ada di storage/app/public/qrcode, generate sekarang
        if (!file_exists($fullPath) || filesize($fullPath) === 0) {
            QRCode::generate($identifier, $fullPath, [
                's'  => 'qrm',
                'sf' => 8,
                'p'  => 20,
            ]);
        }

        return [
            'identifier' => $identifier,
            'filename'   => $filename,
            'url'        => asset("storage/{$storageRelativePath}"),
            'path'       => $fullPath,
        ];
    }

    /**
     * Sinkronkan seluruh data QR code untuk semua pengguna di database.
     */
    public static function syncAll()
    {
        $synced = [
            'admin' => 0,
            'guru'  => 0,
            'murid' => 0,
        ];

        // 1. Admin
        $admins = Admin::with('user')->get();
        foreach ($admins as $admin) {
            if ($admin->user) {
                $nip = $admin->nip ?: (string) $admin->user->id_user;
                $admin->qr_code = $nip;
                $admin->save();
                self::getOrGenerateUserQR($admin->user, $admin);
                $synced['admin']++;
            }
        }

        // 2. Guru
        $gurus = Guru::with('user')->get();
        foreach ($gurus as $guru) {
            if ($guru->user) {
                $nip = $guru->nip ?: (string) $guru->user->id_user;
                $guru->qr_code = $nip;
                $guru->save();
                self::getOrGenerateUserQR($guru->user, $guru);
                $synced['guru']++;
            }
        }

        // 3. Murid
        $murids = Murid::with('user')->get();
        foreach ($murids as $murid) {
            if ($murid->user) {
                $nisn = $murid->nisn ?: (string) $murid->user->id_user;
                $murid->qr_code = $nisn;
                $murid->save();
                self::getOrGenerateUserQR($murid->user, $murid);
                $synced['murid']++;
            }
        }

        return $synced;
    }
}
