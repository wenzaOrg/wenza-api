<?php

namespace App\Http\Resources;

/** @mixin Module */

use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModuleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Module $resource */
        $resource = $this->resource;

        return [
            'id' => $resource->id,
            'title' => $resource->title,
            'order' => $resource->order,
            'description' => $resource->description,
            'lessons' => LessonResource::collection($this->whenLoaded('lessons')),
        ];
    }
}
