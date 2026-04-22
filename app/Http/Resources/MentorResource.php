<?php

namespace App\Http\Resources;

use App\Models\Mentor;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Mentor */
class MentorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Mentor $resource */
        $resource = $this->resource;

        return [
            'id' => $resource->id,
            'name' => "{$resource->first_name} {$resource->last_name}",
            'first_name' => $resource->first_name,
            'last_name' => $resource->last_name,
            'title' => $resource->title,
            'bio' => $resource->bio,
            'avatar_url' => $resource->avatar_url,
            'linkedin_url' => $resource->linkedin_url,
            'years_experience' => $resource->years_experience,
            'courses' => CourseResource::collection($this->whenLoaded('courses')),
        ];
    }
}
