<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use ApiResponse;

    /**
     * Exact controller body from §6.3.
     */
    public function __invoke(LoginRequest $request): JsonResponse
    {
        if (! Auth::attempt($request->only('email', 'password'))) {
            return $this->error('Invalid credentials', 401);
        }

        /** @var User $user */
        $user = $request->user();
        $token = $user->createToken('wenza-frontend')->plainTextToken;

        return $this->success([
            'user' => new UserResource($user),
            'token' => $token,
        ], 'Login successful');
    }
}
