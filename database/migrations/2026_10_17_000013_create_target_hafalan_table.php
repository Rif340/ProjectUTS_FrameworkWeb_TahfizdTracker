<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('target_hafalan', function (Blueprint $table) {
            $table->integer('id_target')->autoIncrement();
            $table->integer('id_kelas');
            $table->integer('id_surat');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();

            $table->foreign('id_kelas')->references('id_kelas')->on('kelas')->onDelete('cascade');
            $table->foreign('id_surat')->references('id_surat')->on('surat')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('target_hafalan');
    }
};
