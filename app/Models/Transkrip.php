<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transkrip extends Model
{
    use HasFactory;

    protected $table = 'transkrip';
    protected $fillable = [
        'file',
        'calon_mahasiswa_id',
        'mata_kuliah_transkrip'
    ];

    protected $casts = [
        'mata_kuliah_transkrip' => 'array'
    ];

    public function calon_mahasiswa()
    {
        return $this->belongsTo(Calon_mahasiswa::class);
    }
}
