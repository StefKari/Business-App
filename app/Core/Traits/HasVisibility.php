<?php

namespace App\Core\Traits;

use App\Models\Role;
use App\Models\User;
use App\Models\VisibilitySetting;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasVisibility
{
    /**
     * Boot the trait
     */
    protected static function bootHasVisibility(): void
    {
        static::created(function ($model) {
            $model->createVisibilitySetting();
        });

        static::deleting(function ($model) {
            $model->visibilitySetting()?->delete();
        });
    }

    /**
     * Get visibility setting
     */
    public function visibilitySetting(): MorphOne
    {
        return $this->morphOne(VisibilitySetting::class, 'model');
    }

    /**
     * Create default visibility setting
     */
    public function createVisibilitySetting(): VisibilitySetting
    {
        return $this->visibilitySetting()->create([
            'created_by' => $this->created_by ?? auth()->id(),
            'visibility_level' => VisibilitySetting::LEVEL_ROLE_BASED,
            'visible_to_roles' => [Role::SYS_ADMIN],
            'is_public' => false,
        ]);
    }

    /**
     * Check if model is visible to user
     */
    public function isVisibleTo(User $user): bool
    {
        // SysAdmin sees everything
        if ($user->isSysAdmin()) {
            return true;
        }

        // Creator always sees their own content
        if ($this->created_by === $user->id) {
            return true;
        }

        $setting = $this->visibilitySetting;

        if (!$setting) {
            return false;
        }

        return $setting->isVisibleToRole($user->role->slug);
    }

    /**
     * Make model public
     */
    public function makePublic(): void
    {
        $setting = $this->visibilitySetting ?? $this->createVisibilitySetting();
        $setting->makePublic();
    }

    /**
     * Make model private
     */
    public function makePrivate(): void
    {
        $setting = $this->visibilitySetting ?? $this->createVisibilitySetting();
        $setting->makePrivate();
    }

    /**
     * Set visible to specific roles
     */
    public function setVisibleToRoles(array $roleSlugs): void
    {
        $setting = $this->visibilitySetting ?? $this->createVisibilitySetting();
        $setting->setVisibleToRoles($roleSlugs);
    }

    /**
     * Scope: Visible to user
     */
    public function scopeVisibleTo($query, User $user)
    {
        // SysAdmin sees everything
        if ($user->isSysAdmin()) {
            return $query;
        }

        $modelType = get_class($this);

        return $query->where(function ($q) use ($user, $modelType) {
            // Created by user
            $q->where('created_by', $user->id)
                // Or has visibility setting allowing this role
                ->orWhereHas('visibilitySetting', function ($visQuery) use ($user) {
                    $visQuery->where('is_public', true)
                        ->orWhere(function ($roleQuery) use ($user) {
                            $roleQuery->where('visibility_level', VisibilitySetting::LEVEL_ALL)
                                ->orWhere(function ($q) use ($user) {
                                    $q->where('visibility_level', VisibilitySetting::LEVEL_ROLE_BASED)
                                        ->whereJsonContains('visible_to_roles', $user->role->slug);
                                });
                        });
                });
        });
    }

    /**
     * Scope: Public only
     */
    public function scopePublicOnly($query)
    {
        return $query->whereHas('visibilitySetting', function ($q) {
            $q->where('is_public', true);
        });
    }
}
