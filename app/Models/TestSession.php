<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_id', 'class_id', 'created_by', 'title', 'duration',
        'starts_at', 'ends_at', 'status',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function test()
    {
        return $this->belongsTo(SchoolTest::class, 'test_id');
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function submissions()
    {
        return $this->hasMany(TestSubmission::class, 'test_session_id');
    }

    public function getDisplayTitleAttribute(): string
    {
        return $this->title ?: ($this->test->title ?? 'Phiên kiểm tra');
    }

    public function getEffectiveDurationAttribute(): int
    {
        return (int) ($this->duration ?: ($this->test->duration ?? 0));
    }

    public function isAvailable(): bool
    {
        return $this->status === 'open'
            && now()->between($this->starts_at, $this->ends_at);
    }
}
