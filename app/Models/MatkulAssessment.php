<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatkulAssessment extends Model
{
    use HasFactory;

    protected $table = 'matkul_assessments';


    protected $fillable = [
        'calon_mahasiswa_id',
        'matkul_id',
        'self_assessment_value',
    ];

    public function calonMahasiswa()
    {
        return $this->belongsTo(Calon_mahasiswa::class);
    }

    public function matkul()
    {
        return $this->belongsTo(Matkul::class);
    }
}
