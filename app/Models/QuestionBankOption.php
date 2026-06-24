<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionBankOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_item_id', 'option_text', 'is_correct', 'order_index',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function bankItem()
    {
        return $this->belongsTo(QuestionBankItem::class, 'bank_item_id');
    }
}
