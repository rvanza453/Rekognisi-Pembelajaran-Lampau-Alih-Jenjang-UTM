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
        Schema::create('self_assessment_camaba', function (Blueprint $table) {
            $table->id();
            $table->enum('nilai',['A','B','C','D']);
            $table->timestamps();

            $table->unsignedBigInteger('calon_mahasiswa_id');
            $table->unsignedBigInteger('cpmk_id');
            $table->unsignedBigInteger('bukti_id');
            $table->foreign('calon_mahasiswa_id')->references('id')->on('calon_mahasiswa')->onDelete('cascade');
            $table->foreign('cpmk_id')->references('id')->on('cpmk')->onDelete('cascade');
            $table->foreign('bukti_id')->references('id')->on('bukti')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('self_assessment_camaba');
    }
};
