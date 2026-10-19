<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi_murid', function (Blueprint $table) {
            $table->integer('id_absensi_murid')->autoIncrement();
            $table->integer('id_murid');
            $table->integer('id_kelas');
            $table->date('tanggal');
            $table->time('waktu_masuk')->nullable();
            $table->time('waktu_pulang')->nullable();
            $table->enum('status', ['tepat waktu', 'terlambat', 'tidak hadir'])->default('tidak hadir');
            $table->timestamps();

            $table->foreign('id_murid')->references('id_murid')->on('murid')->onDelete('cascade');
            $table->foreign('id_kelas')->references('id_kelas')->on('kelas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi_murid');
    }
};
