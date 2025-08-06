<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matkul_score extends Model
{
    use HasFactory;

    protected $table = 'matkul_score';

    protected $fillable = [
        'matkul_id', 
        'assessor_id', 
        'calon_mahasiswa_id',
        'status',
        'nilai',
        'score',
        'nilai_akhir',
        'is_banding',
        'banding_keterangan',
        'banding_status',
    ];

    public function matkul() {
        return $this->belongsTo(Matkul::class, 'matkul_id');
    }

    public function assessor() {
        return $this->belongsTo(Assessor::class, 'assessor_id');
    }

    public function calon_mahasiswa() {
        return $this->belongsTo(Calon_mahasiswa::class, 'calon_mahasiswa_id');
    }
}
