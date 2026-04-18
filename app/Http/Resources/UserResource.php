<?php

namespace App\Http\Resources;

/** @mixin User */

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var User $user */
        $user = $this->resource;

        return [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'full_name' => $user->full_name,
            'email' => $user->email,
            'role' => $user->role,
            'avatar_url' => $user->avatar_url,
            'email_verified_at' => $user->email_verified_at?->toIso8601String(),
            'created_at' => $user->created_at?->toIso8601String(),
        ];
    }
}
