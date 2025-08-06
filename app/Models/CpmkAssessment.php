<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CpmkAssessment extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cpmk_assessments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'calon_mahasiswa_id',
        'matkul_id',
        'cpmk_id',
        'matkul_dasar',
        'nilai_matkul_dasar',
        'self_assessment_value',
        'nilai_assessor1',
        'nilai_assessor2',
        'nilai_assessor3',
    ];

    public function calonMahasiswa()
    {
        return $this->belongsTo(Calon_mahasiswa::class, 'calon_mahasiswa_id');
    }

    public function matkul()
    {
        return $this->belongsTo(Matkul::class, 'matkul_id');
    }

    public function cpmk()
    {
        return $this->belongsTo(Cpmk::class, 'cpmk_id');
    }
}