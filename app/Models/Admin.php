<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;

    protected $table = 'admin';
    protected $primaryKey = 'id_admin';

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

    public function absensi()
    {
        return $this->hasMany(AbsensiAdmin::class, 'id_admin', 'id_admin');
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
