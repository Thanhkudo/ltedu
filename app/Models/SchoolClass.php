<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * DÃ¹ng tÃªn SchoolClass Ä‘á»ƒ trÃ¡nh conflict vá»›i tá»« khÃ³a 'class' cá»§a PHP.
 * Map vá»›i báº£ng 'classes' trong DB.
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

    // â”€â”€â”€ Relations â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /** Há»c viÃªn thuá»™c lá»›p nÃ y */
    public function students()
    {
        return $this->belongsToMany(Student::class, 'class_student', 'class_id', 'student_id')
                    ->withPivot('enrolled_at', 'status')
                    ->withTimestamps();
    }

    /** Chá»‰ láº¥y há»c viÃªn Ä‘ang active */
    public function activeStudents()
    {
        return $this->students()->wherePivot('status', 'active');
    }

    /** Buá»•i há»c cá»§a lá»›p */
    public function sessions()
    {
        return $this->hasMany(ClassSession::class, 'class_id')->orderBy('session_number');
    }

    /** BÃ i kiá»ƒm tra cá»§a lá»›p */
    public function tests()
    {
        return $this->hasMany(SchoolTest::class, 'class_id');
    }

    public function testSessions()
    {
        return $this->hasMany(TestSession::class, 'class_id');
    }}



