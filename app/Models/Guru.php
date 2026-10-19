<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    use HasFactory;

    protected $table = 'guru';
    protected $primaryKey = 'id_guru';

    protected $fillable = [
        'id_user',
        'nip',
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
        return $this->hasMany(Kelas::class, 'id_guru', 'id_guru');
    }

    public function absensi()
    {
        return $this->hasMany(AbsensiGuru::class, 'id_guru', 'id_guru');
    }

    public function setoranDisimak()
    {
        return $this->hasMany(SetoranHafalan::class, 'id_guru', 'id_guru');
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
