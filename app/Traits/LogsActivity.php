<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait LogsActivity
{
    protected static function bootLogsActivity(): void
    {
        static::created(fn ($model) => static::writeLog('created', $model));
        static::updated(fn ($model) => static::writeLog('updated', $model));
        static::deleted(fn ($model) => static::writeLog('deleted', $model));
    }

    protected static function writeLog(string $event, $model): void
    {
        $label = method_exists($model, 'getLogLabel') ? $model->getLogLabel() : ($model->title ?? $model->name ?? "#{$model->id}");

        ActivityLog::create([
            'channel' => 'model',
            'level' => 'info',
            'message' => class_basename($model)." {$event}: {$label}",
            'context' => ['model' => class_basename($model), 'id' => $model->id, 'event' => $event],
        ]);
    }
}
