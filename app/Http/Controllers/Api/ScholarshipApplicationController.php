<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreScholarshipApplicationRequest;
use App\Jobs\NotifyAdminOfNewScholarshipApplication;
use App\Jobs\SendScholarshipApplicantConfirmation;
use App\Models\ScholarshipApplication;
use App\Services\TurnstileVerifier;
use Illuminate\Http\JsonResponse;

class ScholarshipApplicationController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected TurnstileVerifier $turnstile
    ) {}

    /**
     * Store a new scholarship application.
     */
    public function store(StoreScholarshipApplicationRequest $request): JsonResponse
    {
        if (! $this->turnstile->verify($request->turnstile_token, $request->ip() ?? '')) {
            return $this->error('Security verification failed. Please try again.', 422);
        }

        $application = ScholarshipApplication::create($request->validated());

        SendScholarshipApplicantConfirmation::dispatch($application);
        NotifyAdminOfNewScholarshipApplication::dispatch($application);

        return $this->created(
            ['reference_code' => $application->reference_code],
            'Scholarship application submitted successfully'
        );
    }
}
