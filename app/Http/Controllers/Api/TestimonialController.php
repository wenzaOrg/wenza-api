<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\TestimonialResource;
use App\Models\Testimonial;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    use ApiResponse;

    /**
     * Paginated testimonials, optionally filtered by is_featured.
     */
    public function index(Request $request): JsonResponse
    {
        $testimonials = Testimonial::query()
            ->when(
                $request->boolean('is_featured', false) || $request->has('is_featured'),
                function ($q) use ($request) {
                    if ($request->has('is_featured')) {
                        $q->where('is_featured', filter_var($request->is_featured, FILTER_VALIDATE_BOOLEAN));
                    }
                }
            )
            ->orderByDesc('created_at')
            ->paginate($request->per_page ?? 15);

        return $this->paginated($testimonials, 'Testimonials retrieved', TestimonialResource::class);
    }
}
