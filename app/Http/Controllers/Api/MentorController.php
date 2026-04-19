<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\MentorResource;
use App\Models\Mentor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MentorController extends Controller
{
    use ApiResponse;

    /**
     * Paginated mentors, optionally filtered by course_id.
     */
    public function index(Request $request): JsonResponse
    {
        $mentors = Mentor::query()
            ->when($request->course_id, function ($q, $courseId) {
                $q->whereHas('courses', fn ($c) => $c->where('courses.id', $courseId));
            })
            ->orderBy('first_name')
            ->paginate($request->per_page ?? 15);

        return $this->paginated($mentors, 'Mentors retrieved', MentorResource::class);
    }
}
