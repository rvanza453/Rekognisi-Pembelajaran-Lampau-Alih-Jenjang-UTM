<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periode', function (Blueprint $table) {
            $table->id();
            $table->string('tahun_ajaran');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        // Add periode_id to calon_mahasiswa table
        Schema::table('calon_mahasiswa', function (Blueprint $table) {
            $table->unsignedBigInteger('periode_id')->nullable();
            $table->foreign('periode_id')->references('id')->on('periode')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('calon_mahasiswa', function (Blueprint $table) {
            $table->dropForeign(['periode_id']);
            $table->dropColumn('periode_id');
        });
        Schema::dropIfExists('periode');
    }
};