<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_id', 'test_session_id', 'student_id', 'total_score',
        'status', 'started_at', 'submitted_at',
    ];

    protected $casts = [
        'started_at'  => 'datetime',
        'submitted_at'=> 'datetime',
        'total_score' => 'float',
    ];

    // â”€â”€â”€ Relations â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public function test()
    {
        return $this->belongsTo(SchoolTest::class, 'test_id');
    }

    public function testSession()
    {
        return $this->belongsTo(TestSession::class, 'test_session_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function answers()
    {
        return $this->hasMany(TestAnswer::class, 'test_submission_id');
    }

    // â”€â”€â”€ Helpers â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public function isSubmitted(): bool
    {
        return in_array($this->status, ['submitted', 'graded']);
    }
}

