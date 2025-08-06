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
        Schema::table('assessment', function (Blueprint $table) {
            // Menambahkan kolom untuk menyimpan timestamp kapan mahasiswa submit
            $table->timestamp('self_assessment_submitted_at')->nullable()->after('rpl_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessment', function (Blueprint $table) {
            $table->dropColumn('self_assessment_submitted_at');
        });
    }
};
