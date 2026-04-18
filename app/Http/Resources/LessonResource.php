<?php

namespace App\Http\Resources;

/** @mixin Lesson */

use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Lesson $resource */
        $resource = $this->resource;

        return [
            'id' => $resource->id,
            'title' => $resource->title,
            'order' => $resource->order,
            'content_type' => $resource->content_type,
            'duration_minutes' => $resource->duration_minutes,
        ];
    }
}
