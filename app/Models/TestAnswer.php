<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_submission_id', 'question_id',
        'selected_option_id', 'answer_text',
        'is_correct', 'score',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'score'      => 'float',
    ];

    // ─── Relations ─────────────────────────────────────────────
    public function submission()
    {
        return $this->belongsTo(TestSubmission::class, 'test_submission_id');
    }

    public function question()
    {
        return $this->belongsTo(TestQuestion::class, 'question_id');
    }

    public function selectedOption()
    {
        return $this->belongsTo(QuestionOption::class, 'selected_option_id');
    }
}
