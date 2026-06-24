<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Dùng tên ClassSession để tránh conflict với Session facade của Laravel.
 * Map với bảng 'sessions' trong DB.
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

    // ─── Relations ─────────────────────────────────────────────
    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /** Bài tập được giao trong buổi học này */
    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'session_id');
    }
}
