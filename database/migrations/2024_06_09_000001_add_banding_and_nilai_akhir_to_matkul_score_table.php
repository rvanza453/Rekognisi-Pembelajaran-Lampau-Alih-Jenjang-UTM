<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('matkul_score', function (Blueprint $table) {
            $table->integer('nilai_akhir')->nullable()->after('score');
            $table->boolean('is_banding')->default(false)->after('nilai_akhir');
            $table->text('banding_keterangan')->nullable()->after('is_banding');
            $table->enum('banding_status', ['pending', 'ditolak', 'diterima'])->default('pending')->after('banding_keterangan');
        });
    }

    public function down()
    {
        Schema::table('matkul_score', function (Blueprint $table) {
            $table->dropColumn(['nilai_akhir', 'is_banding', 'banding_keterangan', 'banding_status']);
        });
    }
}; 