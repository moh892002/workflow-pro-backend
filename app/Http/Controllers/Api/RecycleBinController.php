<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RecycleBinResource;
use App\Services\RecycleBinService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RecycleBinController extends Controller
{
    public function __construct(
        private readonly RecycleBinService $recycleBinService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $data = $this->recycleBinService->list($request->only([
            'model', 'table', 'search', 'per_page', 'page',
        ]));

        return $this->successResponse(
            'Deleted records retrieved successfully',
            $data
        );
    }

    public function showByModel(string $model, Request $request): JsonResponse
    {
        $data = $this->recycleBinService->showByModel($model, $request->only([
            'table', 'search', 'per_page',
        ]));

        return $this->successResponse(
            "Deleted records for {$model} retrieved successfully",
            $data
        );
    }

    public function restore(string $model, int $id): JsonResponse
    {
        try {
            $recycleBin = $this->recycleBinService->restore($model, $id);

            return $this->successResponse(
                'Record restored successfully',
                new RecycleBinResource($recycleBin)
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    public function forceDelete(string $model, int $id): JsonResponse
    {
        try {
            $recycleBin = $this->recycleBinService->forceDelete($model, $id);

            return $this->successResponse(
                'Record permanently deleted successfully',
                new RecycleBinResource($recycleBin)
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    public function bulkRestore(Request $request): JsonResponse
    {
        $validator = validator($request->all(), [
            'records' => 'required|array',
            'records.*.model' => 'required|string',
            'records.*.id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(
                'Validation failed',
                Response::HTTP_BAD_REQUEST,
                $validator->errors()
            );
        }

        try {
            $restored = $this->recycleBinService->bulkRestore($request->input('records'));

            return $this->successResponse(
                'Records restored successfully',
                RecycleBinResource::collection($restored)
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function bulkForceDelete(Request $request): JsonResponse
    {
        $validator = validator($request->all(), [
            'records' => 'required|array',
            'records.*.model' => 'required|string',
            'records.*.id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(
                'Validation failed',
                Response::HTTP_BAD_REQUEST,
                $validator->errors()
            );
        }

        try {
            $deleted = $this->recycleBinService->bulkForceDelete($request->input('records'));

            return $this->successResponse(
                'Records permanently deleted successfully',
                RecycleBinResource::collection($deleted)
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    protected function successResponse(string $message, $data = null, int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    protected function errorResponse(string $message, int $statusCode = Response::HTTP_BAD_REQUEST, $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }
}
