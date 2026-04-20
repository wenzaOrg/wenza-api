<?php

namespace App\Http\Resources;

use App\Models\ScholarshipApplication;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin ScholarshipApplication */
class ScholarshipApplicationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var ScholarshipApplication $resource */
        $resource = $this->resource;

        return [
            'id' => $resource->id,
            'reference_code' => $resource->reference_code,
            'first_name' => $resource->first_name,
            'last_name' => $resource->last_name,
            'email' => $resource->email,
            'phone' => $resource->phone,
            'gender' => $resource->gender,
            'country' => $resource->country,
            'state_or_city' => $resource->state_or_city,
            'current_status' => $resource->current_status,
            'education_level' => $resource->education_level,
            'learning_mode' => $resource->learning_mode,
            'wants_scholarship' => $resource->wants_scholarship,
            'prior_tech_experience' => $resource->prior_tech_experience,
            'wants_job_placement' => $resource->wants_job_placement,
            'pipeline_status' => $resource->pipeline_status,
            'admin_notes' => $resource->admin_notes,
            'course' => new CourseResource($this->whenLoaded('course')),
            'cohort' => new CohortResource($this->whenLoaded('cohort')),
            'created_at' => $resource->created_at?->toIso8601String(),
            'updated_at' => $resource->updated_at?->toIso8601String(),
        ];
    }
}
