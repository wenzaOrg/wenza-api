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
     * Paginated, filterable course catalogue — exact pattern from §4.4.
     */
    public function index(Request $request): JsonResponse
    {
        $courses = Course::query()
            ->where('is_published', true)
            ->when($request->category, fn ($q, $cat) => $q->where('category', $cat))
            ->orderBy('title')
            ->paginate($request->per_page ?? 15);

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
     * Single course with modules, cohorts, and mentors.
     */
    public function show(string $slug): JsonResponse
    {
        $course = Course::with(['modules.lessons', 'cohorts', 'mentors'])
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return $this->success(
            new CourseResource($course),
            'Course retrieved'
        );
    }
}
