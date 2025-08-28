<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContactMetaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\ContactMeta::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $contact = $this->route('contact');

        return [
            'key' => [
                'required',
                'string',
                'max:100',
                Rule::unique('contact_metas')->where(function ($query) use ($contact) {
                    return $query->where('contact_id', $contact->id);
                }),
            ],
            'value' => 'required|string|max:1000',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'key.unique' => 'This key already exists for this contact.',
            'key.max' => 'The key may not be greater than 100 characters.',
            'value.max' => 'The value may not be greater than 1000 characters.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $contact = $this->route('contact');

            // Check if contact already has 5 meta fields
            if ($contact && $contact->meta()->count() >= 5) {
                $validator->errors()->add('key', 'This contact already has the maximum of 5 custom fields.');
            }
        });
    }
}
