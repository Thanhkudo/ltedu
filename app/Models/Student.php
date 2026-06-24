<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'student_code', 'full_name',
        'email', 'phone', 'date_of_birth', 'address',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    // ─── Relations ─────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Danh sách lớp học viên đang/đã tham gia */
    public function classes()
    {
        return $this->belongsToMany(SchoolClass::class, 'class_student', 'student_id', 'class_id')
                    ->withPivot('enrolled_at', 'status')
                    ->withTimestamps();
    }

    /** Bài tập đã nộp */
    public function assignmentSubmissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    /** Bài kiểm tra đã làm */
    public function testSubmissions()
    {
        return $this->hasMany(TestSubmission::class);
    }
}
