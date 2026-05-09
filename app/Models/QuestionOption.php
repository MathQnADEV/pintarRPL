<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['question_id', 'option_text', 'is_correct'])]
class QuestionOption extends Model
{
    use SoftDeletes;

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
