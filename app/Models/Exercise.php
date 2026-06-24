<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'content',
        'type', 'difficulty', 'created_by',
    ];

    // ─── Relations ─────────────────────────────────────────────
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Bài tập này đã được giao trong những assignment nào */
    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }
}
