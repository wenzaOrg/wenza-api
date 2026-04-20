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
            'reference_code' => $resource->reference_code,
            'full_name' => $resource->full_name,
            'email' => $resource->email,
            'phone' => $resource->phone,
            'age' => $resource->age,
            'employment_status' => $resource->employment_status,
            'education_level' => $resource->education_level,
            'goals' => $resource->goals,
            'course_id' => $resource->course_id,
            'wants_scholarship' => (bool) $resource->wants_scholarship,
            'pipeline_status' => $resource->pipeline_status,
            'admin_notes' => $resource->admin_notes,
            'created_at' => $resource->created_at?->toIso8601String(),
            'updated_at' => $resource->updated_at?->toIso8601String(),
        ];
    }
}
