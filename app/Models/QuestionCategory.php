<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'grade_level', 'skill_type', 'topic', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function bankItems()
    {
        return $this->hasMany(QuestionBankItem::class, 'category_id');
    }

    public function questionGroups()
    {
        return $this->hasMany(QuestionGroup::class, 'category_id');
    }
}
