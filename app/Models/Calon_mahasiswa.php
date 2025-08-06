<?php

namespace App\Models;

use App\Models\Scopes\JenisMahasiswaScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calon_mahasiswa extends Model
{
    use HasFactory;

    protected $table = 'calon_mahasiswa';
    protected $fillable = [
        'user_id',
        'jenis_mahasiswa',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'nomor_telepon',
        'nomor_rumah',
        'nomor_kantor',
        'kelamin',
        'alamat',
        'kota',
        'provinsi',
        'kode_pos',
        'kebangsaan',
        'foto_profile',
        'jurusan_id',
        'periode_id'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    protected static function booted(): void
    {
        static::addGlobalScope(new JenisMahasiswaScope);
    }
    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }
    public function ijazah()
    {
        return $this->hasOne(Ijazah::class);
    }
    public function self_assessment_camaba()
    {
        return $this->hasOne(Self_assessment_camaba::class);
    }
    public function assessment()
    {
        return $this->hasOne(Assessment::class);
    }
    public function transkrip()
    {
        return $this->hasOne(Transkrip::class);
    }
    public function bukti_alih_jenjang()
    {
        return $this->hasMany(bukti_alih_jenjang::class);
    }
    public function periode()
    {
        return $this->belongsTo(Periode::class);
    }
}
