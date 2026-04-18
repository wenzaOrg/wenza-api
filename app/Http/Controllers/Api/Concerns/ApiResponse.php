<?php

namespace App\Http\Controllers\Api\Concerns;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponse
{
    protected function success(
        mixed $data = null,
        string $message = 'Success',
        int $status = 200
    ): JsonResponse {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    protected function error(
        string $message,
        int $status = 400,
        ?array $errors = null
    ): JsonResponse {
        $payload = ['status' => 'error', 'message' => $message];

        if ($errors !== null) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $status);
    }

    protected function paginated(
        LengthAwarePaginator $paginator,
        string $message = 'Records retrieved',
        ?string $resourceClass = null
    ): JsonResponse {
        $records = $resourceClass
            ? $resourceClass::collection($paginator->items())
            : $paginator->items();

        return $this->success([
            'records' => $records,
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
            'total' => $paginator->total(),
            'first_page_url' => $paginator->url(1),
            'last_page_url' => $paginator->url($paginator->lastPage()),
            'next_page_url' => $paginator->nextPageUrl(),
            'prev_page_url' => $paginator->previousPageUrl(),
            'path' => $paginator->path(),
            'links' => $paginator->linkCollection()->toArray(),
        ], $message);
    }
}
