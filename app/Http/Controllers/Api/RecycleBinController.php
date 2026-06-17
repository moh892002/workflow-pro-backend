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
        return $this->success(
            $this->recycleBinService->list($request->only(['model', 'table', 'search', 'per_page', 'page'])),
            'Deleted records retrieved successfully'
        );
    }

    public function showByModel(string $model, Request $request): JsonResponse
    {
        return $this->success(
            $this->recycleBinService->showByModel($model, $request->only(['table', 'search', 'per_page'])),
            "Deleted records for {$model} retrieved successfully"
        );
    }

    public function restore(string $model, int $id): JsonResponse
    {
        try {
            $recycleBin = $this->recycleBinService->restore($model, $id);

            return $this->success(new RecycleBinResource($recycleBin), 'Record restored successfully');
        } catch (\Exception $e) {
            return $this->error('Record not found or could not be restored', Response::HTTP_NOT_FOUND);
        }
    }

    public function forceDelete(string $model, int $id): JsonResponse
    {
        try {
            $recycleBin = $this->recycleBinService->forceDelete($model, $id);

            return $this->success(new RecycleBinResource($recycleBin), 'Record permanently deleted successfully');
        } catch (\Exception $e) {
            return $this->error('Record not found or could not be deleted', Response::HTTP_NOT_FOUND);
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
            return $this->error('Validation failed', Response::HTTP_BAD_REQUEST, $validator->errors());
        }

        try {
            $restored = $this->recycleBinService->bulkRestore($request->input('records'));

            return $this->success(RecycleBinResource::collection($restored), 'Records restored successfully');
        } catch (\Exception $e) {
            return $this->error('Bulk restore failed', Response::HTTP_BAD_REQUEST);
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
            return $this->error('Validation failed', Response::HTTP_BAD_REQUEST, $validator->errors());
        }

        try {
            $deleted = $this->recycleBinService->bulkForceDelete($request->input('records'));

            return $this->success(RecycleBinResource::collection($deleted), 'Records permanently deleted successfully');
        } catch (\Exception $e) {
            return $this->error('Bulk force delete failed', Response::HTTP_BAD_REQUEST);
        }
    }
}
