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

    //  Helpers 
    public function isAdmin(): bool    { return $this->role === 'admin'; }
    public function isTeacher(): bool  { return $this->role === 'teacher'; }
    public function isStudent(): bool  { return $this->role === 'student'; }

    //  Relations 
    /** Lop hoc ma user nay la giao vien phu trach */
    public function teachingClasses()
    {
        return $this->hasMany(SchoolClass::class, 'teacher_id');
    }

    /** Bai tap user nay tao ra */
    public function exercises()
    {
        return $this->hasMany(Exercise::class, 'created_by');
    }

    /** Profile hoc vien (neu user co role = student) */
    public function student()
    {
        return $this->hasOne(Student::class);
    }
}
