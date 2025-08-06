<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ijazah extends Model
{
    use HasFactory;

    protected $table = 'ijazah';
    protected $fillable = [
        'calon_mahasiswa_id',
        'institusi_pendidikan',
        'jenjang',
        'provinsi',
        'kota',
        'negara',
        'fakultas',
        'jurusan',
        'ipk_nilai',
        'tahun_lulus',
        'file'
    ];
    public function calon_mahasiswa()
    {
        return $this->belongsTo(Calon_mahasiswa::class);
    }
}
