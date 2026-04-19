<?php

namespace App\Http\Resources;

use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Lead */
class LeadResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Lead $resource */
        $resource = $this->resource;

        return [
            'id' => $resource->id,
            'reference' => $resource->reference,
            'full_name' => $resource->full_name,
            'email' => $resource->email,
            'phone' => $resource->phone,
            'course_id' => $resource->course_id,
            'referral_source' => $resource->referral_source,
            'status' => $resource->status,
            'created_at' => $resource->created_at?->toIso8601String(),
        ];
    }
}
