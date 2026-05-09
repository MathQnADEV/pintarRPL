<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds two columns to support the full learning flow:
     *   - is_posttest : marks questions that belong to a level's post-test pool
     *   - level       : stores which level a post-test question targets
     *                   (nullable — only used when is_posttest = true)
     */
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            // Post-test flag — analogous to is_pretest
            $table->boolean('is_posttest')->default(false)->after('is_pretest');

            // Level the post-test question belongs to (pemula / menengah / lanjut).
            // Also available for pretest questions that are level-tagged, though
            // pretest scoring will assign level dynamically via rule-based logic.
            $table->enum('level', ['pemula', 'menengah', 'lanjut'])
                ->nullable()
                ->after('is_posttest');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['is_posttest', 'level']);
        });
    }
};
