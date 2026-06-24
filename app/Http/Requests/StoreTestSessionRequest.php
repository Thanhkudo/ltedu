<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTestSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'test_id' => 'required|exists:tests,id',
            'class_id' => 'required|exists:classes,id',
            'title' => 'nullable|string|max:255',
            'duration' => 'nullable|integer|min:1|max:300',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'status' => 'required|in:draft,open,closed',
        ];
    }
}
