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
        Schema::create('cpmk_assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('calon_mahasiswa_id');
            $table->unsignedBigInteger('matkul_id');
            $table->unsignedBigInteger('cpmk_id');
            $table->string('matkul_dasar')->nullable();
            $table->string('nilai_matkul_dasar')->nullable();
            $table->string('self_assessment_value')->nullable(); // opsional, jika ingin tetap simpan
            $table->float('nilai_assessor1')->nullable();
            $table->float('nilai_assessor2')->nullable();
            $table->float('nilai_assessor3')->nullable();
            $table->timestamps();

            $table->foreign('calon_mahasiswa_id')->references('id')->on('calon_mahasiswa')->onDelete('cascade');
            $table->foreign('matkul_id')->references('id')->on('matkul')->onDelete('cascade');
            $table->foreign('cpmk_id')->references('id')->on('cpmk')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cpmk_assessments');
    }
}; 