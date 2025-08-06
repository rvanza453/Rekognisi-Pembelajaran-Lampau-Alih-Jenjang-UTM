<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class JenisMahasiswaScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Ambil tipe aplikasi dari file konfigurasi (yang membaca .env)
        $appType = config('app.type');

        if ($appType === 'alih_jenjang') {
            $builder->where('jenis_mahasiswa', 'camaba_alihjenjang');
        } elseif ($appType === 'eporto') {
            $builder->where('jenis_mahasiswa', 'camaba_eporto');
        }
        // Jika app.type tidak di-set, maka tidak ada filter yang diterapkan.
        // Ini berguna untuk environment development atau testing.
    }
}