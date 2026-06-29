<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Dung ten SchoolClass de tranh conflict voi tu khoa 'class' cua PHP.
 * Map voi bang 'classes' trong DB.
 */
class SchoolClass extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'class_code', 'name', 'description',
        'teacher_id', 'start_date', 'end_date', 'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    // a"a"a" Relations a"a"a"a"a"a"a"a"a"a"a"a"a"a"a"a"a"a"a"a"a"a"a"a"a"a"a"a"a"a"a"a"a"a"a"a"a"a"a"a"a"a"a"a"a"
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /** Hoc vien thuoc lop nay */
    public function students()
    {
        return $this->belongsToMany(Student::class, 'class_student', 'class_id', 'student_id')
                    ->withPivot('enrolled_at', 'status')
                    ->withTimestamps();
    }

    /** Chi lay hoc vien dang active */
    public function activeStudents()
    {
        return $this->students()->wherePivot('status', 'active');
    }

    /** Buoi hoc cua lop */
    public function sessions()
    {
        return $this->hasMany(ClassSession::class, 'class_id')->orderBy('session_number');
    }

}
