<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('murid', function (Blueprint $table) {
            $table->integer('id_murid')->autoIncrement();
            $table->integer('id_user');
            $table->integer('id_kelas')->nullable();
            $table->string('nisn', 30)->nullable()->unique();
            $table->string('no_hp', 20)->nullable();
            $table->string('qr_code')->nullable();
            $table->string('foto')->nullable();
            $table->enum('status', ['Aktif', 'Nonaktif'])->default('Aktif');
            $table->timestamps();

            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
            $table->foreign('id_kelas')->references('id_kelas')->on('kelas')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('murid');
    }
};
