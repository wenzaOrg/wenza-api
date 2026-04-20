<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Public endpoint
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:200'],
            'phone' => ['required', 'string', 'regex:/^(\+234|0)[789][01]\d{8}$/', 'max:20'],
            'age' => ['required', 'integer', 'min:16', 'max:80'],
            'employment_status' => ['required', 'string', Rule::in(['employed', 'self_employed', 'unemployed', 'student', 'other'])],
            'education_level' => ['required', 'string', Rule::in(['ssce', 'ond', 'hnd', 'bachelors', 'masters', 'phd', 'other'])],
            'goals' => ['required', 'string', 'min:20', 'max:2000'],
            'course_id' => ['nullable', 'integer', 'exists:courses,id'],
            'wants_scholarship' => ['required', 'boolean'],
            'turnstile_token' => ['required', 'string'],
            'guardian_consent' => [
                $this->integer('age') < 18 ? 'required' : 'nullable',
                $this->integer('age') < 18 ? 'accepted' : 'sometimes',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'full_name.required' => 'Please provide your full name.',
            'email.email' => 'Please enter a valid email address.',
            'phone.regex' => 'Please provide a valid Nigerian phone number.',
            'age.min' => 'You must be at least 16 years old to apply.',
            'goals.min' => 'Please provide more detail about your goals (at least 20 characters).',
            'guardian_consent.accepted' => 'Guardian consent is required for applicants under 18 years of age.',
            'turnstile_token.required' => 'Security verification is required.',
        ];
    }
}
