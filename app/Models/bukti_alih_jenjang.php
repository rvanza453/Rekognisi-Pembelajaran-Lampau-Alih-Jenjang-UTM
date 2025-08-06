<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class bukti_alih_jenjang extends Model
{
    use HasFactory;

    protected $table = 'bukti_alih_jenjang';
    protected $primaryKey = 'nomor_dokumen';
    public $incrementing = true;
    protected $keyType = 'integer';
    protected $fillable = [
        'jenis_dokumen',
        'calon_mahasiswa_id',
        'file'
    ];

    public function calon_mahasiswa()
    {
        return $this->belongsTo(Calon_mahasiswa::class);
    }
}
