<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_id', 'question_text', 'question_type',
        'score', 'order_index',
    ];

    protected $casts = [
        'score' => 'float',
    ];

    // ─── Relations ─────────────────────────────────────────────
    public function test()
    {
        return $this->belongsTo(SchoolTest::class, 'test_id');
    }

    /** Các lựa chọn đáp án (dành cho multiple_choice & true_false) */
    public function options()
    {
        return $this->hasMany(QuestionOption::class, 'question_id')->orderBy('order_index');
    }

    public function correctOption()
    {
        return $this->hasOne(QuestionOption::class, 'question_id')->where('is_correct', true);
    }
}
