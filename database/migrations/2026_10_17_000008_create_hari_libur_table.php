<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hari_libur', function (Blueprint $table) {
            $table->integer('id_hari_libur')->autoIncrement();
            $table->integer('id_tahun_akademik');
            $table->string('keterangan', 255);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->enum('tipe', ['Nasional', 'Sekolah', 'Khusus'])->default('Sekolah');
            $table->enum('status', ['Aktif', 'Nonaktif'])->default('Aktif');
            $table->timestamps();

            $table->foreign('id_tahun_akademik')->references('id_tahun_akademik')->on('tahun_akademik')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hari_libur');
    }
};
