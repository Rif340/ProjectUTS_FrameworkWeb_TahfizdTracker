<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsensiMurid extends Model
{
    use HasFactory;

    protected $table = 'absensi_murid';
    protected $primaryKey = 'id_absensi_murid';

    protected $fillable = [
        'id_murid',
        'id_kelas',
        'tanggal',
        'waktu_masuk',
        'waktu_pulang',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function murid()
    {
        return $this->belongsTo(Murid::class, 'id_murid', 'id_murid');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }
}
