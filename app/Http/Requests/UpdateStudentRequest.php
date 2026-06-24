<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $studentId = $this->route('student');

        return [
            'full_name'     => 'sometimes|required|string|max:255',
            'email'         => "sometimes|required|email|unique:students,email,{$studentId}",
            'phone'         => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'address'       => 'nullable|string|max:500',
        ];
    }
}
