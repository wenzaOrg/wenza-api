<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    use ApiResponse;

    /**
     * Revoke the current access token per §6.4.
     */
    public function __invoke(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $user->currentAccessToken()->delete();

        return $this->success(null, 'Logged out');
    }
}
