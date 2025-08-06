<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cpmk', function (Blueprint $table) {
            // Ubah kolom penjelasan dari VARCHAR ke TEXT
            $table->text('penjelasan')->change();
            
            // Tambah kolom kode_cpmk setelah kolom id
            $table->string('kode_cpmk', 20)->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('cpmk', function (Blueprint $table) {
            // Kembalikan kolom penjelasan ke VARCHAR
            $table->string('penjelasan')->change();
            
            // Hapus kolom kode_cpmk
            $table->dropColumn('kode_cpmk');
        });
    }
}; 