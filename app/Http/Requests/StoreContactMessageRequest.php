<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactMessageRequest extends FormRequest
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
            'subject' => ['required', 'in:application_question,scholarship_question,press_partnerships,other'],
            'message' => ['required', 'string', 'min:20', 'max:2000'],
            'turnstile_token' => ['required', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'full_name.required' => 'Please provide your full name.',
            'full_name.min' => 'Your name must be at least 2 characters long.',
            'full_name.max' => 'Your name must not exceed 120 characters.',
            'email.required' => 'Please provide your email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'Your email address must not exceed 200 characters.',
            'subject.required' => 'Please select a subject for your message.',
            'subject.in' => 'Please select a valid subject.',
            'message.required' => 'Please provide your message.',
            'message.min' => 'Your message must be at least 20 characters long.',
            'message.max' => 'Your message must not exceed 2000 characters.',
            'turnstile_token.required' => 'Security verification is required.',
        ];
    }
}
