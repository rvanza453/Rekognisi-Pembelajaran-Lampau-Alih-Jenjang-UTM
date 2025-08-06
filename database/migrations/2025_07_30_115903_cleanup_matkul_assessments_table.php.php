<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration will remove columns that are no longer needed from the matkul_assessments table.
     */
    public function up(): void
    {
        Schema::table('matkul_assessments', function (Blueprint $table) {
            // First, drop foreign keys if they exist to avoid errors.
            // Laravel's dropColumn might handle this, but being explicit is safer.
            // Note: Foreign key names might differ if not explicitly named. Default is table_column_foreign.
            $table->dropForeign(['assessor1_id']);
            $table->dropForeign(['assessor2_id']);
            $table->dropForeign(['assessor3_id']);

            // Now, drop the columns.
            $table->dropColumn([
                'matkul_dasar',
                'nilai_matkul_dasar',
                'assessor1_assessment',
                'assessor2_assessment',
                'assessor3_assessment',
                'assessor1_id',
                'assessor2_id',
                'assessor3_id',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * This will add the columns back if the migration is rolled back.
     */
    public function down(): void
    {
        // This part is intentionally left empty as it's a cleanup migration.
        // Re-adding columns could lead to data inconsistencies.
        // If you need rollback capability, you can add the column definitions back here.
    }
};
