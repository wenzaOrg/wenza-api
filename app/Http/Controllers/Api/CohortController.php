<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\CohortResource;
use App\Models\Cohort;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CohortController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $query = Cohort::where('status', 'upcoming');

        if ($courseId = $request->query('course_id')) {
            $query->where('course_id', $courseId);
        }

        $cohorts = $query->orderBy('start_date')->get();

        return $this->success(
            CohortResource::collection($cohorts),
            'Cohorts retrieved'
        );
    }
}
