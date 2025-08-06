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
        Schema::create('matkul_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calon_mahasiswa_id')->constrained('calon_mahasiswa')->onDelete('cascade');
            $table->foreignId('matkul_id')->constrained('matkul')->onDelete('cascade');
            $table->string('self_assessment_value');
            $table->string('assessor1_assessment')->nullable();
            $table->string('assessor2_assessment')->nullable();
            $table->string('assessor3_assessment')->nullable();
            $table->foreignId('assessor1_id')->nullable()->constrained('assessor')->onDelete('set null');
            $table->foreignId('assessor2_id')->nullable()->constrained('assessor')->onDelete('set null');
            $table->foreignId('assessor3_id')->nullable()->constrained('assessor')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matkul_assessments');
    }
};
