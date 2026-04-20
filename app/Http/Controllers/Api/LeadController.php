<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLeadRequest;
use App\Jobs\NotifyAdminOfNewLead;
use App\Jobs\SendApplicantConfirmation;
use App\Models\Lead;
use App\Services\TurnstileVerifier;
use Illuminate\Http\JsonResponse;

class LeadController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected TurnstileVerifier $turnstile
    ) {}

    /**
     * Store a new lead (apply form submission).
     */
    public function store(StoreLeadRequest $request): JsonResponse
    {
        // Verify Turnstile CAPTCHA token
        if (! $this->turnstile->verify($request->turnstile_token, $request->ip() ?? '')) {
            return $this->error(
                'Security verification failed. Please try again.',
                422
            );
        }

        // Create the lead (reference_code auto-generated via model boot hook)
        $lead = Lead::create($request->validated());

        // Dispatch notifications (queued, best-effort — don't fail submission on email errors)
        SendApplicantConfirmation::dispatch($lead);
        NotifyAdminOfNewLead::dispatch($lead);

        return $this->created(
            ['reference_code' => $lead->reference_code],
            'Application submitted successfully'
        );
    }
}
