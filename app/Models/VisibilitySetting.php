<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class VisibilitySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'model_type',
        'model_id',
        'created_by',
        'visibility_level',
        'visible_to_roles',
        'is_public',
    ];

    protected $casts = [
        'visible_to_roles' => 'array',
        'is_public' => 'boolean',
    ];

    // Visibility levels
    const LEVEL_ALL = 'all';
    const LEVEL_ROLE_BASED = 'role_based';
    const LEVEL_CUSTOM = 'custom';
    const LEVEL_PRIVATE = 'private';

    /**
     * Get the creator
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the subject model (polymorphic)
     */
    public function subject(): MorphTo
    {
        return $this->morphTo('model');
    }

    /**
     * Check if visible to role
     */
    public function isVisibleToRole(string $roleSlug): bool
    {
        if ($this->visibility_level === self::LEVEL_ALL) {
            return true;
        }

        if ($this->visibility_level === self::LEVEL_PRIVATE) {
            return false;
        }

        if ($this->is_public) {
            return true;
        }

        if (empty($this->visible_to_roles)) {
            return false;
        }

        return in_array($roleSlug, $this->visible_to_roles);
    }

    /**
     * Make public
     */
    public function makePublic(): void
    {
        $this->update([
            'is_public' => true,
            'visibility_level' => self::LEVEL_ALL,
        ]);
    }

    /**
     * Make private
     */
    public function makePrivate(): void
    {
        $this->update([
            'is_public' => false,
            'visibility_level' => self::LEVEL_PRIVATE,
            'visible_to_roles' => [],
        ]);
    }

    /**
     * Set visible to specific roles
     */
    public function setVisibleToRoles(array $roleSlugs): void
    {
        $this->update([
            'visibility_level' => self::LEVEL_ROLE_BASED,
            'visible_to_roles' => $roleSlugs,
        ]);
    }
}
