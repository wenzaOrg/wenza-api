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
     * Paginated, filterable course catalog — exact pattern from §4.4.
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
     * Single course with modules + cohorts.
     */
    public function show(string $slug): JsonResponse
    {
        $course = Course::with(['modules.lessons', 'cohorts'])
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return $this->success(
            new CourseResource($course),
            'Course retrieved'
        );
    }
}
