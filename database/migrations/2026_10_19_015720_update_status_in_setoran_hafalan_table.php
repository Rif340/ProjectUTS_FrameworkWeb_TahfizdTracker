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
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE setoran_hafalan MODIFY COLUMN status VARCHAR(50) DEFAULT 'Lulus'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE setoran_hafalan MODIFY COLUMN status ENUM('lancar', 'kurang lancar', 'mengulang') DEFAULT 'lancar'");
    }
};
