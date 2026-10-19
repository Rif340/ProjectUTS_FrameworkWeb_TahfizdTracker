<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:sync-qr-codes', function () {
    $this->info('Starting QR Code synchronization...');
    $synced = \App\Services\QRCodeService::syncAll();
    $this->info("Synchronization finished!");
    $this->line("Admin: {$synced['admin']}");
    $this->line("Guru:  {$synced['guru']}");
    $this->line("Murid: {$synced['murid']}");
})->purpose('Synchronize QR code values to NIP/NISN and generate storage QR images');

