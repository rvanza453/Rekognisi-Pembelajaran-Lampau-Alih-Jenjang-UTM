<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Periode extends Model
{
    use HasFactory;

    protected $table = 'periode';
    protected $fillable = ['tahun_ajaran', 'is_active'];

    public function calon_mahasiswa()
    {
        return $this->hasMany(Calon_mahasiswa::class);
    }

    public static function getActivePeriode()
    {
        return self::where('is_active', true)->first();
    }
} 