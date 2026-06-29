<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Dung ten ClassSession de tranh conflict voi Session facade cua Laravel.
 * Map voi bang 'sessions' trong DB.
 */
class ClassSession extends Model
{
    use HasFactory;

    protected $table = 'sessions';

    protected $fillable = [
        'class_id', 'session_number', 'title',
        'description', 'session_date', 'status',
    ];

    protected $casts = [
        'session_date' => 'datetime',
    ];

    //  Relations 
    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /** Bai tap duoc giao trong buoi hoc nay */
    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'session_id');
    }
}
