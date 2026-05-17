<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds a new question type (code_arrange) and an `order` column to
     * question_options so each option can define its correct position.
     *
     *  questions.type  : ENUM extended with 'code_arrange'
     *  question_options.order : SMALLINT UNSIGNED NULLABLE
     *                          Only used when parent question.type = 'code_arrange'.
     *                          Defines the correct sequence (1 = first line).
     */
    public function up(): void
    {
        // Extend the enum — raw statement is safest across all MySQL versions.
        DB::statement("ALTER TABLE `questions` MODIFY COLUMN `type` ENUM('multiple_choice','code_arrange') NOT NULL DEFAULT 'multiple_choice'");

        Schema::table('question_options', function (Blueprint $table) {
            $table->unsignedSmallInteger('order')->nullable()->after('is_correct');
        });
    }

    public function down(): void
    {
        Schema::table('question_options', function (Blueprint $table) {
            $table->dropColumn('order');
        });

        DB::statement("ALTER TABLE `questions` MODIFY COLUMN `type` ENUM('multiple_choice') NOT NULL DEFAULT 'multiple_choice'");
    }
};
