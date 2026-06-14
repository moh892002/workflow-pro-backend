<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RecycleBin;
use App\Http\Resources\RecycleBinResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;

class RecycleBinController extends Controller
{
    /**
     * Normalize model string to full namespace.
     *
     * @param string $model
     * @return string
     */
    protected function normalizeModel(string $model): string
    {
        if (str_contains($model, '\\')) {
            return $model;
        }

        return 'App\\Models\\' . $model;
    }

    /**
     * Display a listing of the deleted records.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = RecycleBin::query();

        // Filter by model/table
        if ($request->has('model') && $request->input('model')) {
            $query->where('deleted_model', $this->normalizeModel($request->input('model')));
        }

        if ($request->has('table') && $request->input('table')) {
            $query->where('deleted_table_name', $request->input('table'));
        }

        // Search in deleted_data (JSONB) - using proper JSONB operators for PostgreSQL
        if ($request->has('search') && $request->input('search')) {
            $search = $request->input('search');
            // Using ->> to extract JSON value as text, then LIKE for pattern matching
            $query->whereRaw("deleted_data::text LIKE ?", ["%{$search}%"]);
        }

        // Pagination
        $perPage = $request->input('per_page', 15);
        $page = $request->input('page', 1);
        $cacheKey = 'recycle_bin:index:' . md5(implode(':', [
            $request->input('model', ''),
            $request->input('table', ''),
            $request->input('search', ''),
            $perPage,
            $page,
        ]));

        $recycleBins = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($query, $perPage) {
            return RecycleBinResource::collection($query->latest()->paginate($perPage))->resolve();
        });

        return $this->successResponse(
            'Deleted records retrieved successfully',
            $recycleBins
        );
    }

    /**
     * Display deleted records for a specific model.
     *
     * @param string $model
     * @param Request $request
     * @return JsonResponse
     */
    public function showByModel(string $model, Request $request): JsonResponse
    {
        $model = $this->normalizeModel($model);

        $query = RecycleBin::where('deleted_model', $model);

        // Optional: filter by table if provided
        if ($request->has('table') && $request->input('table')) {
            $query->where('deleted_table_name', $request->input('table'));
        }

        // Search
        if ($request->has('search') && $request->input('search')) {
            $search = $request->input('search');
            // Using ->> to extract JSON value as text, then LIKE for pattern matching
            $query->whereRaw("deleted_data::text LIKE ?", ["%{$search}%"]);
        }

        $perPage = $request->input('per_page', 15);
        $recycleBins = $query->latest()->paginate($perPage);

        return $this->successResponse(
            "Deleted records for {$model} retrieved successfully",
            RecycleBinResource::collection($recycleBins)
        );
    }

    /**
     * Restore a soft deleted record.
     *
     * @param string $model
     * @param int $id
     * @return JsonResponse
     */
    public function restore(string $model, int $id): JsonResponse
    {
        $model = $this->normalizeModel($model);
        DB::beginTransaction();
        try {
            $recycleBin = RecycleBin::where('deleted_model', $model)
                ->where('deleted_item_id', $id)
                ->firstOrFail();

            // Check if the model class exists
            if (!class_exists($model)) {
                throw new \Exception("Model {$model} does not exist.");
            }

            // Find the original model instance (including soft deleted)
            $modelInstance = $model::withTrashed()->find($id);

            if (!$modelInstance) {
                throw new ModelNotFoundException("No instance of {$model} with id {$id} found.");
            }

            // Restore the model instance
            $modelInstance->restore();

            // Delete the recycle bin entry
            $recycleBin->delete();

            DB::commit();

            return $this->successResponse(
                "Record restored successfully",
                new RecycleBinResource($recycleBin)
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Permanently delete a record from the recycle bin.
     *
     * @param string $model
     * @param int $id
     * @return JsonResponse
     */
    public function forceDelete(string $model, int $id): JsonResponse
    {
        $model = $this->normalizeModel($model);
        try {
            $recycleBin = RecycleBin::where('deleted_model', $model)
                ->where('deleted_item_id', $id)
                ->firstOrFail();

            // Delete the recycle bin entry (permanent delete)
            $recycleBin->forceDelete();

            return $this->successResponse(
                "Record permanently deleted successfully",
                new RecycleBinResource($recycleBin)
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Restore multiple soft deleted records.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function bulkRestore(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'records' => 'required|array',
            'records.*.model' => 'required|string',
            'records.*.id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', Response::HTTP_BAD_REQUEST, $validator->errors());
        }

        DB::beginTransaction();
        try {
            $restored = [];
            foreach ($request->input('records') as $record) {
                $model = $this->normalizeModel($record['model']);
                $id = $record['id'];

                // Check if the model class exists
                if (!class_exists($model)) {
                    throw new \Exception("Model {$model} does not exist.");
                }

                $recycleBin = RecycleBin::where('deleted_model', $model)
                    ->where('deleted_item_id', $id)
                    ->firstOrFail();

                // Find the original model instance (including soft deleted)
                $modelInstance = $model::withTrashed()->find($id);

                if (!$modelInstance) {
                    throw new ModelNotFoundException("No instance of {$model} with id {$id} found.");
                }

                // Restore the model instance
                $modelInstance->restore();

                // Delete the recycle bin entry
                $recycleBin->delete();

                $restored[] = new RecycleBinResource($recycleBin);
            }

            DB::commit();

            return $this->successResponse(
                'Records restored successfully',
                $restored
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Permanently delete multiple records from the recycle bin.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function bulkForceDelete(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'records' => 'required|array',
            'records.*.model' => 'required|string',
            'records.*.id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', Response::HTTP_BAD_REQUEST, $validator->errors());
        }

        try {
            $deleted = [];
            foreach ($request->input('records') as $record) {
                $model = $this->normalizeModel($record['model']);
                $id = $record['id'];

                $recycleBin = RecycleBin::where('deleted_model', $model)
                    ->where('deleted_item_id', $id)
                    ->firstOrFail();

                // Permanent delete
                $recycleBin->forceDelete();

                $deleted[] = new RecycleBinResource($recycleBin);
            }

            return $this->successResponse(
                'Records permanently deleted successfully',
                $deleted
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Return a success response.
     *
     * @param string $message
     * @param mixed $data
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function successResponse(string $message, $data = null, int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Return an error response.
     *
     * @param string $message
     * @param int $statusCode
     * @param mixed $errors
     * @return JsonResponse
     */
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