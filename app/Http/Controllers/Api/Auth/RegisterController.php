<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class RegisterController extends Controller
{
    use ApiResponse;

    public function __invoke(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => $request->password,
            'role' => 'student',
        ]);

        // Assign the student role via Spatie Permission
        $user->assignRole('student');

        $token = $user->createToken('wenza-frontend')->plainTextToken;

        return $this->success([
            'user' => new UserResource($user),
            'token' => $token,
        ], 'Registration successful', 201);
    }
}
