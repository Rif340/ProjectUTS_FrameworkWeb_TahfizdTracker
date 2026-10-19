<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HariKerja extends Model
{
    use HasFactory;

    protected $table = 'hari_kerja';
    protected $primaryKey = 'id_hari_kerja';

    protected $fillable = [
        'hari',
        'jam_masuk',
        'jam_pulang',
        'status',
    ];
}
