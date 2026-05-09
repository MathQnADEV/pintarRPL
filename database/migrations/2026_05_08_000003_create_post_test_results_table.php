<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_test_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('from_level', ['pemula', 'menengah', 'lanjut']);
            $table->integer('score');                      // 0-100
            $table->boolean('passed')->default(false);     // lulus jika score >= threshold
            $table->timestamp('completed_at');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_test_results');
    }
};
