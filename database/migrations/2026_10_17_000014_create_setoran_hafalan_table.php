<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('setoran_hafalan', function (Blueprint $table) {
            $table->integer('id_setoran')->autoIncrement();
            $table->integer('id_murid');
            $table->integer('id_kelas');
            $table->integer('id_guru');
            $table->integer('id_surat');
            $table->integer('ayat_mulai');
            $table->integer('ayat_selesai');
            $table->date('tanggal');
            $table->enum('status', ['lancar', 'kurang lancar', 'mengulang'])->default('lancar');
            $table->timestamps();

            $table->foreign('id_murid')->references('id_murid')->on('murid')->onDelete('cascade');
            $table->foreign('id_kelas')->references('id_kelas')->on('kelas')->onDelete('cascade');
            $table->foreign('id_guru')->references('id_guru')->on('guru')->onDelete('cascade');
            $table->foreign('id_surat')->references('id_surat')->on('surat')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setoran_hafalan');
    }
};
