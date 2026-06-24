<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * DÃ¹ng tÃªn SchoolTest Ä‘á»ƒ trÃ¡nh conflict vá»›i PHPUnit Test class.
 * Map vá»›i báº£ng 'tests' trong DB.
 */
class SchoolTest extends Model
{
    use HasFactory;

    protected $table = 'tests';

    protected $fillable = [
        'class_id', 'created_by', 'title', 'description',
        'duration', 'total_score', 'starts_at', 'ends_at', 'status',
    ];

    protected $casts = [
        'starts_at'   => 'datetime',
        'ends_at'     => 'datetime',
        'total_score' => 'float',
    ];

    // â”€â”€â”€ Relations â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions()
    {
        return $this->hasMany(TestQuestion::class, 'test_id')->orderBy('order_index');
    }

    public function sessions()
    {
        return $this->hasMany(TestSession::class, 'test_id')->latest('starts_at');
    }

    public function submissions()
    {
        return $this->hasMany(TestSubmission::class, 'test_id');
    }

    // â”€â”€â”€ Helpers â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public function isAvailable(): bool
    {
        return $this->status === 'published'
            && now()->between($this->starts_at, $this->ends_at);
    }

    public function submissionOf(int $studentId): ?TestSubmission
    {
        return $this->submissions()->where('student_id', $studentId)->first();
    }
}


