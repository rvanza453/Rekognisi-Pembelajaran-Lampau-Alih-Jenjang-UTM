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
            $table->timestamp('assessor_1_submitted_at')->nullable()->after('deadline');
            $table->timestamp('assessor_2_submitted_at')->nullable()->after('assessor_1_submitted_at');
            $table->timestamp('assessor_3_submitted_at')->nullable()->after('assessor_2_submitted_at');
            $table->timestamp('published_at')->nullable()->after('assessor_3_submitted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessment', function (Blueprint $table) {
            $table->dropColumn(['assessor_1_submitted_at', 'assessor_2_submitted_at', 'assessor_3_submitted_at', 'published_at']);
        });
    }
};
