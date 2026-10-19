<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsensiAdmin extends Model
{
    use HasFactory;

    protected $table = 'absensi_admin';
    protected $primaryKey = 'id_absensi_admin';

    protected $fillable = [
        'id_admin',
        'tanggal',
        'waktu_masuk',
        'waktu_pulang',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'id_admin', 'id_admin');
    }
}
