<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssignmentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'session_id'   => 'required|exists:sessions,id',
            'instructions' => 'nullable|string',
            'due_date'     => 'required|date|after:now',
            'max_score'    => 'nullable|integer|min:1|max:1000',

            'grade_level'  => 'required|integer|in:6,7,8,9',
            'skill_type'   => 'required|in:listening,speaking,reading,writing,grammar,vocabulary',
            'question_configs' => 'required|array|min:1',
            'question_configs.*.category_id' => 'nullable|exists:question_categories,id',
            'question_configs.*.answer_mode' => 'nullable|in:select,input',
            'question_configs.*.context_type' => 'nullable|in:normal,reading,listening',
            'question_configs.*.interaction_type' => 'nullable|in:normal,ordering,matching',
            'question_configs.*.quantity' => 'required_with:question_configs.*|integer|min:1|max:100',
        ];
    }
}
