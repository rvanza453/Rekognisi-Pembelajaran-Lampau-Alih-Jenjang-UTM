<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessor extends Model
{
    use HasFactory;

    protected $table = 'assessor';
    protected $fillable = [
        'user_id',
        'jurusan_id',
        'nama',
        'alamat',
        'no_hp',
        'foto'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function assessment()
    {
        return $this->hasOne(Assessment::class);
    }
    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }

}
