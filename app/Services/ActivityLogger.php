<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    public static function log($action, $model, $modelId, $oldValues = null, $newValues = null)
    {
        $user = Auth::user();

        ActivityLog::create([
            'user_id' => $user?->id,
            'user_name' => $user?->name ?? 'Anonyme',
            'user_role' => $user?->role ?? 'unknown',
            'action' => $action,
            'model' => class_basename($model),
            'model_id' => $modelId,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public static function logCreate($model, $modelId, $newValues = null)
    {
        self::log('create', $model, $modelId, null, $newValues);
    }

    public static function logUpdate($model, $modelId, $oldValues = null, $newValues = null)
    {
        self::log('update', $model, $modelId, $oldValues, $newValues);
    }

    public static function logDelete($model, $modelId, $oldValues = null)
    {
        self::log('delete', $model, $modelId, $oldValues, null);
    }
}
