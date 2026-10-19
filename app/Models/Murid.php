<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Murid extends Model
{
    use HasFactory;

    protected $table = 'murid';
    protected $primaryKey = 'id_murid';

    protected $fillable = [
        'id_user',
        'id_kelas',
        'nisn',
        'no_hp',
        'qr_code',
        'foto',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }

    public function absensi()
    {
        return $this->hasMany(AbsensiMurid::class, 'id_murid', 'id_murid');
    }

    public function setoran()
    {
        return $this->hasMany(SetoranHafalan::class, 'id_murid', 'id_murid');
    }

    public function getFotoUrlAttribute()
    {
        if (empty($this->foto)) {
            return null;
        }

        if (str_starts_with($this->foto, 'http://') || str_starts_with($this->foto, 'https://')) {
            return $this->foto;
        }

        $cleanPath = ltrim(str_replace('storage/', '', $this->foto), '/');
        return asset('storage/' . $cleanPath);
    }
}
