<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengaturan_absensi', function (Blueprint $table) {
            $table->id('id_pengaturan');
            $table->string('nama_lokasi', 100);
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->unsignedInteger('radius_meter')->default(100);
            $table->enum('status', ['Aktif', 'Nonaktif'])->default('Aktif');
            $table->timestamps();
        });

        // Masukkan data awal SDIT At-Taubah Karawang
        DB::table('pengaturan_absensi')->insert([
            'nama_lokasi'  => 'SDIT At-Taubah Karawang',
            'latitude'     => -6.3045200,
            'longitude'    => 107.3031000,
            'radius_meter' => 100,
            'status'       => 'Aktif',
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaturan_absensi');
    }
};
