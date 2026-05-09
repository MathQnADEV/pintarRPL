<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['title', 'description', 'content', 'level', 'order_position', 'is_active'])]
class Topic extends Model
{
    use SoftDeletes;

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function quizQuestions(): HasMany
    {
        return $this->hasMany(Question::class)
            ->where('is_pretest', false)
            ->where('is_posttest', false);
    }

    public function learningProgress(): HasMany
    {
        return $this->hasMany(LearningProgress::class);
    }

    public function quizResults(): HasMany
    {
        return $this->hasMany(QuizResult::class);
    }
}
