<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matkul extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'nama_matkul',
        'jurusan_id',
        'sinonim',
        'kode_matkul',
        'sks'
    ];
    protected $table = 'matkul';

    public $timestamps = false;

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class,'jurusan_id','id');
    }
    public function cpmk()
    {
        // Pastikan relasinya adalah hasMany
        return $this->hasMany(Cpmk::class, 'matkul_id');
    }

    public function getSinonimArrayAttribute()
    {
        return $this->sinonim ? json_decode($this->sinonim, true) : [];
    }
    
}
