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
        Schema::table('calon_mahasiswa', function (Blueprint $table) {
            // Add new columns
            $table->string('nomor_rumah')->nullable()->after('tanggal_lahir');
            $table->string('nomor_kantor')->nullable()->after('nomor_rumah');
            $table->string('kebangsaan')->nullable()->after('kode_pos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calon_mahasiswa', function (Blueprint $table) {
            // Remove columns
            $table->dropColumn(['nomor_rumah', 'nomor_kantor', 'kebangsaan']);
        });
    }
};
