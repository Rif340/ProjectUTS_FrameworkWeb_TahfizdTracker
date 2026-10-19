<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';
    protected $primaryKey = 'id_kelas';

    protected $fillable = [
        'nama_kelas',
        'id_guru',
        'id_tahun_akademik',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'status',
    ];

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru', 'id_guru');
    }

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class, 'id_tahun_akademik', 'id_tahun_akademik');
    }

    public function murid()
    {
        return $this->hasMany(Murid::class, 'id_kelas', 'id_kelas');
    }

    public function targetHafalan()
    {
        return $this->hasMany(TargetHafalan::class, 'id_kelas', 'id_kelas');
    }

    public function setoran()
    {
        return $this->hasMany(SetoranHafalan::class, 'id_kelas', 'id_kelas');
    }

    public function absensiMurid()
    {
        return $this->hasMany(AbsensiMurid::class, 'id_kelas', 'id_kelas');
    }

    public function absensiGuru()
    {
        return $this->hasMany(AbsensiGuru::class, 'id_kelas', 'id_kelas');
    }
}
