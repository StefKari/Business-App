<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'model_type',
        'model_id',
        'action',
        'old_values',
        'new_values',
        'changes',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'changes' => 'array',
    ];

    // Action constants
    const ACTION_CREATED = 'created';
    const ACTION_UPDATED = 'updated';
    const ACTION_DELETED = 'deleted';
    const ACTION_RESTORED = 'restored';
    const ACTION_VIEWED = 'viewed';

    /**
     * Get the user who performed the action
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subject model (polymorphic)
     */
    public function subject(): MorphTo
    {
        return $this->morphTo('model');
    }

    /**
     * Get formatted action name
     */
    public function getFormattedActionAttribute(): string
    {
        return ucfirst(str_replace('_', ' ', $this->action));
    }

    /**
     * Get description of changes
     */
    public function getDescriptionAttribute(): string
    {
        $userName = $this->user ? $this->user->name : 'System';
        $modelName = class_basename($this->model_type);

        return "{$userName} {$this->formatted_action} {$modelName} #{$this->model_id}";
    }

    /**
     * Scope: Recent activities
     */
    public function scopeRecent($query, int $limit = 20)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    /**
     * Scope: By user
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: By model
     */
    public function scopeByModel($query, string $modelType, ?int $modelId = null)
    {
        $query = $query->where('model_type', $modelType);

        if ($modelId !== null) {
            $query->where('model_id', $modelId);
        }

        return $query;
    }

    /**
     * Scope: By action
     */
    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }
}
