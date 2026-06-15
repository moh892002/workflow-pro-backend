<?php

namespace App\Services;

use App\Models\RecycleBin;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RecycleBinService
{
    public function list(array $params): array
    {
        $query = RecycleBin::query();

        if (! empty($params['model'])) {
            $query->where('deleted_model', $this->normalizeModel($params['model']));
        }

        if (! empty($params['table'])) {
            $query->where('deleted_table_name', $params['table']);
        }

        if (! empty($params['search'])) {
            $query->whereRaw('deleted_data::text LIKE ?', ["%{$params['search']}%"]);
        }

        $perPage = $params['per_page'] ?? 15;
        $page = $params['page'] ?? 1;
        $cacheKey = 'recycle_bin:index:' . md5(implode(':', [
            $params['model'] ?? '',
            $params['table'] ?? '',
            $params['search'] ?? '',
            $perPage,
            $page,
        ]));

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($query, $perPage) {
            return $query->latest()->paginate($perPage)->toArray();
        });
    }

    public function showByModel(string $model, array $params): array
    {
        $model = $this->normalizeModel($model);
        $query = RecycleBin::where('deleted_model', $model);

        if (! empty($params['table'])) {
            $query->where('deleted_table_name', $params['table']);
        }

        if (! empty($params['search'])) {
            $query->whereRaw('deleted_data::text LIKE ?', ["%{$params['search']}%"]);
        }

        $perPage = $params['per_page'] ?? 15;

        return $query->latest()->paginate($perPage)->toArray();
    }

    public function restore(string $model, int $id): RecycleBin
    {
        $model = $this->normalizeModel($model);

        return DB::transaction(function () use ($model, $id) {
            $recycleBin = RecycleBin::where('deleted_model', $model)
                ->where('deleted_item_id', $id)
                ->firstOrFail();

            if (! class_exists($model)) {
                throw new \Exception("Model {$model} does not exist.");
            }

            $modelInstance = $model::withTrashed()->find($id);

            if (! $modelInstance) {
                throw new ModelNotFoundException("No instance of {$model} with id {$id} found.");
            }

            $modelInstance->restore();
            $recycleBin->delete();

            return $recycleBin;
        });
    }

    public function forceDelete(string $model, int $id): RecycleBin
    {
        $model = $this->normalizeModel($model);

        $recycleBin = RecycleBin::where('deleted_model', $model)
            ->where('deleted_item_id', $id)
            ->firstOrFail();

        $recycleBin->forceDelete();

        return $recycleBin;
    }

    public function bulkRestore(array $records): array
    {
        return DB::transaction(function () use ($records) {
            $restored = [];

            foreach ($records as $record) {
                $model = $this->normalizeModel($record['model']);
                $id = $record['id'];

                if (! class_exists($model)) {
                    throw new \Exception("Model {$model} does not exist.");
                }

                $recycleBin = RecycleBin::where('deleted_model', $model)
                    ->where('deleted_item_id', $id)
                    ->firstOrFail();

                $modelInstance = $model::withTrashed()->find($id);

                if (! $modelInstance) {
                    throw new ModelNotFoundException("No instance of {$model} with id {$id} found.");
                }

                $modelInstance->restore();
                $recycleBin->delete();

                $restored[] = $recycleBin;
            }

            return $restored;
        });
    }

    public function bulkForceDelete(array $records): array
    {
        $deleted = [];

        foreach ($records as $record) {
            $model = $this->normalizeModel($record['model']);
            $id = $record['id'];

            $recycleBin = RecycleBin::where('deleted_model', $model)
                ->where('deleted_item_id', $id)
                ->firstOrFail();

            $recycleBin->forceDelete();

            $deleted[] = $recycleBin;
        }

        return $deleted;
    }

    private function normalizeModel(string $model): string
    {
        if (str_contains($model, '\\')) {
            return $model;
        }

        return 'App\\Models\\' . $model;
    }
}
