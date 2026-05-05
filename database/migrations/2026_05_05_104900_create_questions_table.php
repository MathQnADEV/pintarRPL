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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->nullable()->constrained('topics')->onDelete('cascade');
            $table->text('question_text');
            $table->enum('type', ['multiple_choice'])->default('multiple_choice');
            $table->boolean('is_pretest')->default(false); // true = soal pre-test
            $table->text('explanation')->nullable(); // penjelasan jawaban
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
