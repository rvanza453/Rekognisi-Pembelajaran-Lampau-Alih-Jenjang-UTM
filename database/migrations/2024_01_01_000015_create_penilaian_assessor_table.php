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
        Schema::create('penilaian_assessor', function (Blueprint $table) {
            $table->id();
            $table->enum('nilai',['A','B','C','D']);

            $table->unsignedBigInteger('cpmk_id');
            $table->unsignedBigInteger('assessor_id');
            $table->foreign('cpmk_id')->references('id')->on('cpmk')->onDelete('cascade');
            $table->foreign('assessor_id')->references('id')->on('assessor')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaian_assessors');
    }
};
