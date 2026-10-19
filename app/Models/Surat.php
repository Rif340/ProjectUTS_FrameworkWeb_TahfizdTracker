<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Surat extends Model
{
    use HasFactory;

    protected $table = 'surat';
    protected $primaryKey = 'id_surat';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'id_surat',
        'nama_surat',
        'jumlah_ayat',
        'juz',
    ];

    public function targetHafalan()
    {
        return $this->hasMany(TargetHafalan::class, 'id_surat', 'id_surat');
    }

    public function setoran()
    {
        return $this->hasMany(SetoranHafalan::class, 'id_surat', 'id_surat');
    }
}
