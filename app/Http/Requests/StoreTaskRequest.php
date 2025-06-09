<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:3|max:100',
            'description' => 'required|string|min:10|max:5000',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The task name is required.',
            'name.min' => 'The task name must be at least 3 characters.',
            'name.max' => 'The task name must not exceed 100 characters.',
            'description.required' => 'The task description is required.',
            'description.min' => 'The task description must be at least 10 characters.',
            'description.max' => 'The task description must not exceed 5000 characters.',
        ];
    }
}