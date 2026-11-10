<?php

namespace App\Services;

use App\Models\SetoranHafalan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    // format pesan setoran hafalan
    public static function formatSetoranMessage(SetoranHafalan $setoran): string
    {
        $murid = $setoran->murid;
        $namaMurid = $murid->user->nama ?? 'Siswa';
        $namaSurat = $setoran->surat->nama_surat ?? 'Surat';
        $juz = $setoran->surat->juz ?? '-';
        $mulai = $setoran->ayat_mulai;
        $selesai = $setoran->ayat_selesai;
        $status = $setoran->status;
        $tanggal = \Carbon\Carbon::parse($setoran->tanggal)->translatedFormat('d F Y');

        return "Assalamu'alaikum Warahmatullahi Wabarakatuh.\n\n"
             . "Yth. Orang Tua / Wali dari ananda *{$namaMurid}*,\n\n"
             . "Alhamdulillah, ananda telah selesai melakukan setoran hafalan Al-Qur'an di SDIT At-Taubah Karawang dengan rincian:\n"
             . "📖 *Surat*: {$namaSurat} (Juz {$juz})\n"
             . "🔢 *Ayat*: {$mulai} s/d {$selesai}\n"
             . "📊 *Hasil / Status*: {$status}\n"
             . "📅 *Tanggal*: {$tanggal}\n\n"
             . "Semoga ananda istiqomah dalam memelihara dan menghafal Al-Qur'an. Aamiin.\n\n"
             . "_Pesan otomatis dari Sistem Tahfizd Tracker SDIT At-Taubah Karawang_";
    }
    // akhir format pesan setoran hafalan

    // tautan whatsapp langsung
    public static function getWhatsAppUrl(SetoranHafalan $setoran): ?string
    {
        $murid = $setoran->murid;
        if (!$murid || empty($murid->no_hp)) {
            return null;
        }

        $phone = preg_replace('/[^0-9]/', '', $murid->no_hp);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        $message = urlencode(self::formatSetoranMessage($setoran));
        return "https://api.whatsapp.com/send?phone={$phone}&text={$message}";
    }
    // akhir tautan whatsapp langsung

    // kirim notifikasi otomatis
    public static function sendSetoranNotification(SetoranHafalan $setoran): array
    {
        $murid = $setoran->murid;
        if (!$murid || empty($murid->no_hp)) {
            return [
                'status'  => false,
                'message' => 'Nomor HP orang tua / murid belum terdaftar.',
                'wa_url'  => null,
            ];
        }

        $phone = preg_replace('/[^0-9]/', '', $murid->no_hp);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        $message = self::formatSetoranMessage($setoran);

        Log::info("Tahfizd Notification to {$phone}:\n" . $message);

        // kirim melalui webhook n8n
        $n8nWebhookUrl = env('N8N_WEBHOOK_URL');
        $n8nSent = false;
        if (!empty($n8nWebhookUrl)) {
            try {
                $n8nResponse = Http::timeout(2)->post($n8nWebhookUrl, [
                    'event'        => 'setoran_hafalan_created',
                    'id_setoran'   => $setoran->id_setoran,
                    'nama_murid'   => $murid->user->nama ?? 'Siswa',
                    'nisn'         => $murid->nisn ?? '-',
                    'no_hp'        => $phone,
                    'surat'        => $setoran->surat->nama_surat ?? 'Surat',
                    'juz'          => $setoran->surat->juz ?? '-',
                    'ayat_mulai'   => $setoran->ayat_mulai,
                    'ayat_selesai' => $setoran->ayat_selesai,
                    'status'       => $setoran->status,
                    'nilai'        => $setoran->nilai ?? null,
                    'catatan'      => $setoran->catatan ?? null,
                    'tanggal'      => $setoran->tanggal,
                    'message'      => $message,
                    'created_at'   => now()->toDateTimeString(),
                ]);

                if ($n8nResponse->successful()) {
                    $n8nSent = true;
                    Log::info("Notifikasi n8n berhasil dikirim untuk setoran #{$setoran->id_setoran}");
                }
            } catch (\Exception $e) {
                Log::info('n8n Webhook skipped: ' . $e->getMessage());
            }
        }
        // akhir kirim melalui webhook n8n

        // kirim melalui gateway fonnte
        $fonnteToken = env('FONNTE_TOKEN');
        $fonnteSent = false;
        $fonnteMsg = '';

        if (!empty($fonnteToken)) {
            try {
                $response = Http::withoutVerifying()->timeout(8)->withHeaders([
                    'Authorization' => $fonnteToken,
                ])->post('https://api.fonnte.com/send', [
                    'target'      => $phone,
                    'message'     => $message,
                    'countryCode' => '62',
                ]);

                $resJson = $response->json();
                $fonnteSent = ($response->successful() && isset($resJson['status']) && $resJson['status'] == true);
                $fonnteMsg = $resJson['reason'] ?? ($fonnteSent ? 'Terkirim' : 'Gagal');

                Log::info("Fonnte Send to {$phone} - Status: " . ($fonnteSent ? 'SUCCESS' : 'FAILED') . " - Response: " . $response->body());

                return [
                    'status'      => $fonnteSent,
                    'fonnte_sent' => $fonnteSent,
                    'phone'       => $phone,
                    'message'     => $fonnteSent ? 'Notifikasi WhatsApp berhasil terkirim otomatis ke orang tua.' : ('Gagal Fonnte: ' . $fonnteMsg),
                    'n8n'         => $n8nSent,
                    'wa_url'      => self::getWhatsAppUrl($setoran),
                ];
            } catch (\Exception $e) {
                Log::error('Gagal kirim notifikasi via gateway Fonnte: ' . $e->getMessage());
            }
        }
        // akhir kirim melalui gateway fonnte

        return [
            'status'      => false,
            'fonnte_sent' => false,
            'n8n'         => $n8nSent,
            'phone'       => $phone,
            'message'     => 'Siap kirim via direct WA.',
            'wa_url'      => self::getWhatsAppUrl($setoran),
        ];
    }
    // akhir kirim notifikasi otomatis
}
