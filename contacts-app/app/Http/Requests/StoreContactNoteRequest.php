<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactNoteRequest extends FormRequest
{
     /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by policy
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:10000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'body.required' => 'Note content is required.',
            'body.max' => 'Note content cannot exceed 10,000 characters.',
        ];
    }
}
