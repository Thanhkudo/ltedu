<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // ─── Helpers ───────────────────────────────────────────────
    public function isAdmin(): bool    { return $this->role === 'admin'; }
    public function isTeacher(): bool  { return $this->role === 'teacher'; }
    public function isStudent(): bool  { return $this->role === 'student'; }

    // ─── Relations ─────────────────────────────────────────────
    /** Lớp học mà user này là giáo viên phụ trách */
    public function teachingClasses()
    {
        return $this->hasMany(SchoolClass::class, 'teacher_id');
    }

    /** Bài tập user này tạo ra */
    public function exercises()
    {
        return $this->hasMany(Exercise::class, 'created_by');
    }

    /** Bài kiểm tra user này tạo ra */
    public function tests()
    {
        return $this->hasMany(SchoolTest::class, 'created_by');
    }

    /** Profile học viên (nếu user có role = student) */
    public function student()
    {
        return $this->hasOne(Student::class);
    }
}
