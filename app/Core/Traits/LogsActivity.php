<?php

namespace App\Core\Traits;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait LogsActivity
{
    /**
     * Boot the trait
     */
    protected static function bootLogsActivity(): void
    {
        static::created(function ($model) {
            $model->logActivity(ActivityLog::ACTION_CREATED);
        });

        static::updated(function ($model) {
            if ($model->isDirty()) {
                $model->logActivity(ActivityLog::ACTION_UPDATED);
            }
        });

        static::deleted(function ($model) {
            $model->logActivity(ActivityLog::ACTION_DELETED);
        });

        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model) {
                $model->logActivity(ActivityLog::ACTION_RESTORED);
            });
        }
    }

    /**
     * Get activity logs
     */
    public function activityLogs(): MorphMany
    {
        return $this->morphMany(ActivityLog::class, 'model');
    }

    /**
     * Log an activity
     */
    public function logActivity(string $action, ?array $customChanges = null): ActivityLog
    {
        $oldValues = null;
        $newValues = null;
        $changes = $customChanges;

        if ($action === ActivityLog::ACTION_UPDATED && $this->isDirty()) {
            $changed = $this->getDirty();
            $oldValues = array_intersect_key($this->getOriginal(), $changed);
            $newValues = $changed;
            $changes = $changes ?? $this->getChanges();
        } elseif ($action === ActivityLog::ACTION_CREATED) {
            $newValues = $this->getAttributes();
        } elseif ($action === ActivityLog::ACTION_DELETED) {
            $oldValues = $this->getOriginal();
        }

        return ActivityLog::create([
            'user_id' => auth()->id(),
            'model_type' => get_class($this),
            'model_id' => $this->id,
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'changes' => $changes,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Get latest activity
     */
    public function latestActivity(): ?ActivityLog
    {
        return $this->activityLogs()->latest()->first();
    }

    /**
     * Get activity by action
     */
    public function getActivityByAction(string $action)
    {
        return $this->activityLogs()->where('action', $action)->get();
    }
}
