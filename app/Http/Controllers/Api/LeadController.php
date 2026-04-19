<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLeadRequest;
use App\Http\Resources\LeadResource;
use App\Jobs\SendLeadNotification;
use App\Models\Lead;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class LeadController extends Controller
{
    use ApiResponse;

    /**
     * Store a new lead (general enquiry / apply form).
     */
    public function store(StoreLeadRequest $request): JsonResponse
    {
        $lead = Lead::create([
            ...$request->validated(),
            'reference' => 'WZL-'.strtoupper(Str::random(8)),
            'status' => 'new',
        ]);

        SendLeadNotification::dispatch($lead)->onQueue('notifications');

        return $this->success(
            new LeadResource($lead),
            'Your application has been received. We will be in touch shortly.',
            201
        );
    }
}
