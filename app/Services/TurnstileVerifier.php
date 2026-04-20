<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TurnstileVerifier
{
    public function verify(string $token, string $ip): bool
    {
        $secret = config('services.turnstile.secret');
        if (empty($secret)) {
            // In testing, allow bypass when env is not set
            return app()->environment('testing');
        }

        $response = Http::asForm()->post(
            'https://challenges.cloudflare.com/turnstile/v0/siteverify',
            [
                'secret' => $secret,
                'response' => $token,
                'remoteip' => $ip,
            ]
        );

        return $response->successful() && ($response->json('success') === true);
    }
}
