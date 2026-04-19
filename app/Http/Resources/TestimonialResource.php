<?php

namespace App\Http\Resources;

use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Testimonial */
class TestimonialResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Testimonial $resource */
        $resource = $this->resource;

        return [
            'id' => $resource->id,
            'source' => $resource->source,
            'content' => $resource->content,
            'author_name' => $resource->author_name,
            'author_role' => $resource->author_role,
            'is_featured' => (bool) $resource->is_featured,
            'created_at' => $resource->created_at?->toIso8601String(),
        ];
    }
}
