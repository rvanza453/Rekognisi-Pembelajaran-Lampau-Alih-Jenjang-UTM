<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Super_admin extends Model
{
    use HasFactory;

    protected $table = 'super_admin';
    protected $fillable = [
        'user_id',
        'nama',
        'no_hp',
        'alamat',
        'foto'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
