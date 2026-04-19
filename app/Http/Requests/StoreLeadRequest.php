<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Public endpoint
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'course_id' => ['nullable', 'integer', 'exists:courses,id'],
            'referral_source' => ['required', 'string', 'max:100'],
            'motivation' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
