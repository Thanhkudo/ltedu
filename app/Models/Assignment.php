<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id', 'exercise_id', 'generation_mode',
        'generation_config', 'generated_question_count',
        'instructions', 'due_date', 'max_score',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'generation_config' => 'array',
    ];

    // ─── Relations ─────────────────────────────────────────────
    public function session()
    {
        return $this->belongsTo(ClassSession::class, 'session_id');
    }

    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }

    /** Bài làm của học viên cho assignment này */
    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    /** Kiểm tra học viên đã nộp chưa */
    public function submissionOf(int $studentId): ?AssignmentSubmission
    {
        return $this->submissions()
            ->where('student_id', $studentId)
            ->orderByDesc('submitted_at')
            ->orderByDesc('id')
            ->first();
    }

    public function isPastDue(): bool
    {
        return $this->due_date->isPast();
    }

    public function isGeneratedFromQuestionBank(): bool
    {
        return $this->generation_mode === 'random';
    }
}
