<?php

namespace App\Traits;

use App\Models\RecycleBin;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

/**
 * Trait to automatically add soft deleted models to the recycle bin.
 */
trait RecycleBinTrait
{
    use SoftDeletes;

    /**
     * Boot the trait.
     *
     * @return void
     */
    protected static function bootRecycleBinTrait()
    {
        static::deleted(function ($model) {
            // Skip if the model is RecycleBin to avoid saving its own deletion
            if ($model instanceof RecycleBin) {
                return;
            }

            RecycleBin::create([
                'deleted_table_name' => $model->getTable(),
                'deleted_model' => get_class($model),
                'deleted_item_id' => $model->getKey(),
                'deleted_data' => $model->toArray(),
                'deleted_by' => Auth::id() ?? null,
            ]);
        });
    }
}
