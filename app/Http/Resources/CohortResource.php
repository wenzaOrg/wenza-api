<?php

namespace App\Http\Resources;

/** @mixin Cohort */

use App\Models\Cohort;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CohortResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Cohort $resource */
        $resource = $this->resource;

        return [
            'id' => $resource->id,
            'name' => $resource->name,
            'start_date' => $resource->start_date?->toDateString(),
            'end_date' => $resource->end_date?->toDateString(),
            'capacity' => $resource->capacity,
            'status' => $resource->status,
        ];
    }
}
