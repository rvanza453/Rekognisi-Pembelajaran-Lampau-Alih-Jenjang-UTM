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
        Schema::table('matkul_assessments', function (Blueprint $table) {
            $table->string('matkul_dasar')->nullable()->after('self_assessment_value');
            $table->string('nilai_matkul_dasar')->nullable()->after('matkul_dasar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matkul_assessments', function (Blueprint $table) {
            $table->dropColumn(['matkul_dasar', 'nilai_matkul_dasar']);
        });
    }
};
