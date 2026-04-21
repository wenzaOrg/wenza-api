<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactMessageRequest;
use App\Jobs\NotifyAdminOfNewContactMessage;
use App\Models\ContactMessage;
use App\Services\TurnstileVerifier;
use Illuminate\Http\JsonResponse;

class ContactMessageController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected TurnstileVerifier $turnstile
    ) {}

    /**
     * Store a new contact message.
     */
    public function store(StoreContactMessageRequest $request): JsonResponse
    {
        // Verify Turnstile CAPTCHA token
        if (! $this->turnstile->verify($request->turnstile_token, $request->ip() ?? '')) {
            return $this->error(
                'Security verification failed. Please try again.',
                422
            );
        }

        // Create the contact message (reference_code auto-generated via model boot hook)
        $message = ContactMessage::create($request->validated());

        // Dispatch admin notification (queued, best-effort — don't fail submission on email errors)
        NotifyAdminOfNewContactMessage::dispatch($message);

        return $this->created(
            ['reference_code' => $message->reference_code],
            'Message sent successfully'
        );
    }
}
