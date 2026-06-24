<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'full_name'     => 'required|string|max:255',
            'email'         => 'required|email|unique:students,email',
            'student_code'  => 'nullable|string|unique:students,student_code|max:20',
            'phone'         => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'address'       => 'nullable|string|max:500',
            'user_id'       => 'nullable|exists:users,id',
        ];
    }
}
