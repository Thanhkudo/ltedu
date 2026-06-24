<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClassRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'       => 'required|string|max:255',
            'class_code' => 'nullable|string|unique:classes,class_code|max:20',
            'description'=> 'nullable|string',
            'teacher_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date'   => 'nullable|date|after:start_date',
            'status'     => 'nullable|in:active,inactive,completed',
        ];
    }
}
