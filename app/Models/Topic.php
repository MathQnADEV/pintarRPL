<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['title', 'description', 'content', 'video_url', 'level', 'order_position', 'is_active'])]
class Topic extends Model
{
    use SoftDeletes;

    public function getYoutubeEmbedUrl(): ?string
    {
        if (!$this->video_url) {
            return null;
        }

        preg_match(
            '/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([^&\n?#]+)/',
            $this->video_url,
            $matches
        );

        return isset($matches[1])
            ? 'https://www.youtube.com/embed/' . $matches[1]
            : null;
    }

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
