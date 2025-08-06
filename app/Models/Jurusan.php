<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    use HasFactory;

    protected $table = 'jurusan';
    protected $fillable = [
        'nama_jurusan',
        'fakultas_id'
    ];
    public function calon_mahasiswa()
    {
        return $this->hasMany(Calon_mahasiswa::class);
    }

    public function assessor()
    {
        return $this->hasMany(Assessor::class);
    }

    public function admin()
    {
        return $this->hasMany(Admin::class);
    }

    public function assessment()
    {
        return $this->hasMany(Assessment::class);
    }
}
