<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cpmk extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'cpmk';
    protected $fillable = [
        'kode_cpmk',
        'penjelasan',
        'matkul_id'
    ];
    public function self_assessment_camaba()
    {
        return $this->hasOne(Self_assessment_camaba::class);
    }
    public function matkul()
    {
        return $this->belongsTo(Matkul::class);
    }
}
