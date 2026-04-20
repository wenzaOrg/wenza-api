<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    use ApiResponse;

    /**
     * Paginated, filterable course catalogue.
     */
    public function index(Request $request): JsonResponse
    {
        $courses = Course::query()
            ->with(['mentors'])
            ->where('is_published', true)
            ->when($request->category, fn ($q, $cat) => $q->where('category', $cat))
            ->when($request->q, function ($q, $search) {
                $q->where(fn ($sub) => $sub->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%"));
            })
            ->when($request->sort, function ($q, $sort) {
                match ($sort) {
                    'price_low' => $q->orderBy('price_ngn', 'asc'),
                    'price_high' => $q->orderBy('price_ngn', 'desc'),
                    'newest' => $q->orderBy('created_at', 'desc'),
                    default => $q->orderBy('title', 'asc'),
                };
            }, fn ($q) => $q->orderBy('title', 'asc'))
            ->paginate($request->per_page ?? 12);

        return $this->paginated($courses, 'Courses retrieved', CourseResource::class);
    }

    /**
     * Top 4 featured courses for the marketing homepage.
     */
    public function featured(): JsonResponse
    {
        $courses = Course::with(['mentors'])
            ->where('is_published', true)
            ->where('is_featured', true)
            ->take(4)
            ->get();

        return $this->success(
            CourseResource::collection($courses),
            'Featured courses retrieved'
        );
    }

    /**
     * Single course with detail relations.
     */
    public function show(string $slug): JsonResponse
    {
        $course = Course::with(['mentors', 'cohorts' => function ($q) {
            $q->where('status', 'upcoming')->orderBy('start_date', 'asc');
        }])
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return $this->success(
            new CourseResource($course),
            'Course retrieved'
        );
    }
}
