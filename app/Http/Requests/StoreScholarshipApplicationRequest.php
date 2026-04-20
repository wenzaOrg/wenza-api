<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreScholarshipApplicationRequest extends FormRequest
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
            'first_name' => ['required', 'string', 'min:2', 'max:80'],
            'last_name' => ['required', 'string', 'min:2', 'max:80'],
            'email' => ['required', 'email:rfc', 'max:200'],
            'phone' => ['required', 'string', 'regex:/^(\+[0-9]{1,4}|0)[0-9]{7,14}$/'],
            'gender' => ['required', Rule::in(['male', 'female', 'prefer_not_to_say'])],
            'country' => ['required', 'string', 'min:2', 'max:80'],
            'state_or_city' => ['required', 'string', 'min:2', 'max:120'],
            'current_status' => ['required', Rule::in(['student', 'graduate', 'nysc', 'employed', 'self_employed', 'unemployed', 'other'])],
            'education_level' => ['required', Rule::in(['high_school', 'degree', 'masters', 'hnd', 'diploma', 'ond', 'mphil_phd', 'nce', 'other'])],
            'course_id' => ['required', 'exists:courses,id'],
            'cohort_id' => ['required', 'exists:cohorts,id'],
            'learning_mode' => ['required', Rule::in(['online', 'physical', 'hybrid'])],
            'wants_scholarship' => ['required', 'boolean'],
            'prior_tech_experience' => ['required', Rule::in(['none', 'some', 'experienced'])],
            'wants_job_placement' => ['required', 'boolean'],
            'turnstile_token' => ['required', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'Please provide your first name.',
            'last_name.required' => 'Please provide your last name.',
            'email.email' => 'Please provide a valid email address.',
            'phone.regex' => 'Please provide a valid phone number in international or local format.',
            'course_id.exists' => 'The selected programme is invalid.',
            'cohort_id.exists' => 'The selected intake cycle is invalid.',
            'turnstile_token.required' => 'Please complete the security verification.',
        ];
    }
}
