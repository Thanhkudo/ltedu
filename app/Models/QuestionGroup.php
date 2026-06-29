<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'type',
        'title',
        'passage',
        'audio_url',
        'difficulty',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(QuestionCategory::class, 'category_id');
    }

    public function questions()
    {
        return $this->hasMany(QuestionBankItem::class, 'group_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
