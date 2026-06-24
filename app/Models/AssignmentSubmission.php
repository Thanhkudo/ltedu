<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id', 'student_id', 'content',
        'file_path', 'score', 'feedback', 'status', 'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'score'        => 'float',
    ];

    // ─── Relations ─────────────────────────────────────────────
    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // ─── Helpers ───────────────────────────────────────────────
    public function isGraded(): bool
    {
        return $this->status === 'graded';
    }
}
