<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('matkul', function (Blueprint $table) {
            $table->string('kode_matkul')->nullable()->after('nama_matkul');
            $table->integer('sks')->nullable()->after('kode_matkul');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matkul', function (Blueprint $table) {
            $table->dropColumn(['kode_matkul', 'sks']);
        });
    }
};
