<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $options = $this->input('options', []);

        if (is_array($options) && array_is_list($options) && !empty($options) && !is_array($options[0])) {
            $correctIndex = (int) $this->input('correct_option', 0);

            if ($this->input('question_type') === 'true_false') {
                $options = ['Dung', 'Sai'];
            }

            $this->merge([
                'options' => collect($options)->values()->map(function ($text, $index) use ($correctIndex) {
                    return [
                        'option_text' => (string) $text,
                        'is_correct' => $index === $correctIndex,
                        'order_index' => $index + 1,
                    ];
                })->all(),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'question_text' => 'required|string',
            'question_type' => 'required|in:multiple_choice,true_false,short_answer,essay',
            'score' => 'nullable|numeric|min:0',
            'order_index' => 'nullable|integer|min:1',
            'options' => 'required_if:question_type,multiple_choice,true_false|array|min:2',
            'options.*.option_text' => 'required|string',
            'options.*.is_correct' => 'required|boolean',
            'options.*.order_index' => 'nullable|integer|min:1',
        ];
    }
}
