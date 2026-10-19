<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TargetHafalan extends Model
{
    use HasFactory;

    protected $table = 'target_hafalan';
    protected $primaryKey = 'id_target';

    protected $fillable = [
        'id_kelas',
        'id_surat',
        'status',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }

    public function surat()
    {
        return $this->belongsTo(Surat::class, 'id_surat', 'id_surat');
    }
}
