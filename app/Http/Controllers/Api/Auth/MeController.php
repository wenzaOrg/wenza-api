<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeController extends Controller
{
    use ApiResponse;

    public function __invoke(Request $request): JsonResponse
    {
        return $this->success(
            new UserResource($request->user()),
            'User retrieved'
        );
    }
}
