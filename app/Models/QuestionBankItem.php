<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionBankItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'question_text',
        'passage',
        'audio_url',
        'answer_mode',
        'interaction_type',
        'interaction_data',
        'context_type',
        'difficulty',
        'correct_answer',
        'explanation',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'interaction_data' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(QuestionCategory::class, 'category_id');
    }

    public function options()
    {
        return $this->hasMany(QuestionBankOption::class, 'bank_item_id')->orderBy('order_index');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
