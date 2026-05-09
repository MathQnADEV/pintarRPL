<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['topic_id', 'question_text', 'type', 'is_pretest', 'is_posttest', 'level', 'explanation'])]
class Question extends Model
{
    use SoftDeletes;

    // ── Relationships ────────────────────────────────────────────────

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class);
    }

    public function correctOption(): HasMany
    {
        return $this->hasMany(QuestionOption::class)->where('is_correct', true);
    }

    // ── Accessor: virtual "question_category" for Filament forms ────

    /**
     * Derives a human-readable category string from the boolean flags so
     * the Filament form can display a ToggleButtons selector without needing
     * a real DB column.
     *
     * Values: 'pretest' | 'kuis' | 'posttest'
     */
    public function getQuestionCategoryAttribute(): string
    {
        if ($this->is_posttest) {
            return 'posttest';
        }

        if ($this->is_pretest) {
            return 'pretest';
        }

        return 'kuis';
    }

    // ── Scopes ───────────────────────────────────────────────────────

    public function scopePretest($query)
    {
        return $query->where('is_pretest', true);
    }

    public function scopeKuis($query)
    {
        return $query->where('is_pretest', false)->where('is_posttest', false);
    }

    public function scopePosttest($query)
    {
        return $query->where('is_posttest', true);
    }
}
