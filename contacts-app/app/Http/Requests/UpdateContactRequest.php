<?php

namespace App\Http\Requests;

use App\Models\Contact;
use App\Services\CurrentOrganization;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Check if user can update this specific contact
        $contact = $this->route('contact');

        if (!$contact instanceof Contact) {
            return false;
        }

        return $this->user()->can('update', $contact);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $currentOrg = app(CurrentOrganization::class)->get();
        $contact = $this->route('contact');

        return [
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'nullable',
                'email',
                'max:255',
                Rule::unique('contacts', 'email')
                    ->where('organization_id', $currentOrg?->id)
                    ->ignore($contact?->id),
            ],
            'phone' => 'sometimes|nullable|string|max:255',
            'avatar' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'remove_avatar' => 'sometimes|boolean',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'email.unique' => 'This email address is already used by another contact in this organization.',
            'email.email' => 'Please enter a valid email address.',
            'avatar.image' => 'Avatar must be an image file.',
            'avatar.mimes' => 'Avatar must be a JPEG, PNG, JPG, or GIF file.',
            'avatar.max' => 'Avatar file size must not exceed 2MB.',
        ];
    }

    /**
     * Get custom attribute names for validation errors.
     */
    public function attributes(): array
    {
        return [
            'first_name' => 'first name',
            'last_name' => 'last name',
            'email' => 'email address',
            'phone' => 'phone number',
            'avatar' => 'avatar image',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Normalize email to lowercase for consistent validation
        if ($this->has('email') && $this->email) {
            $this->merge([
                'email' => strtolower(trim($this->email)),
            ]);
        }

        // Trim whitespace from name fields
        if ($this->has('first_name')) {
            $this->merge([
                'first_name' => trim($this->first_name),
            ]);
        }

        if ($this->has('last_name')) {
            $this->merge([
                'last_name' => trim($this->last_name),
            ]);
        }
    }

    /**
     * Get validated data with only the fields that were provided.
     */
    public function validatedForUpdate(): array
    {
        $validated = $this->validated();

        // Remove remove_avatar flag from validated data
        unset($validated['remove_avatar']);

        // Handle avatar removal
        if ($this->boolean('remove_avatar')) {
            $validated['avatar_path'] = null;
        }

        return array_filter($validated, function ($value) {
            return $value !== null;
        });
    }
}
