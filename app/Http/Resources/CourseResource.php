<?php

namespace App\Http\Resources;

/** @mixin Course */

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Course $resource */
        $resource = $this->resource;

        return [
            'id' => $resource->id,
            'slug' => $resource->slug,
            'title' => $resource->title,
            'category' => $resource->category,
            'description' => $resource->description,
            'duration_weeks' => $resource->duration_weeks,
            'format' => $resource->format,
            'price_ngn' => $resource->price_ngn,
            'price_usd' => $resource->price_usd,
            'scholarship_price_ngn' => $resource->scholarship_price_ngn,
            'thumbnail_url' => $resource->thumbnail_url,
            'is_published' => $resource->is_published,
            'modules' => ModuleResource::collection($this->whenLoaded('modules')),
            'cohorts' => CohortResource::collection($this->whenLoaded('cohorts')),
            'created_at' => $resource->created_at?->toIso8601String(),
            'updated_at' => $resource->updated_at?->toIso8601String(),
        ];
    }
}
