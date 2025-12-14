<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'level',
    ];

    protected $casts = [
        'level' => 'integer',
    ];

    // Role levels
    const LEVEL_SYS_ADMIN = 100;
    const LEVEL_ADMIN = 50;
    const LEVEL_MODERATOR = 10;

    // Role slugs
    const SYS_ADMIN = 'sys_admin';
    const ADMIN = 'admin';
    const MODERATOR = 'moderator';

    /**
     * Get users with this role
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get permissions for this role
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permission')
            ->withTimestamps();
    }

    /**
     * Check if role is sys admin
     */
    public function isSysAdmin(): bool
    {
        return $this->slug === self::SYS_ADMIN;
    }

    /**
     * Check if role is admin
     */
    public function isAdmin(): bool
    {
        return $this->slug === self::ADMIN;
    }

    /**
     * Check if role is moderator
     */
    public function isModerator(): bool
    {
        return $this->slug === self::MODERATOR;
    }

    /**
     * Check if role has permission
     */
    public function hasPermission(string $permissionSlug): bool
    {
        return $this->permissions()->where('slug', $permissionSlug)->exists();
    }

    /**
     * Assign permission to role
     */
    public function givePermission(Permission $permission): void
    {
        $this->permissions()->syncWithoutDetaching($permission);
    }

    /**
     * Revoke permission from role
     */
    public function revokePermission(Permission $permission): void
    {
        $this->permissions()->detach($permission);
    }

    /**
     * Sync permissions for role
     */
    public function syncPermissions(array $permissionIds): void
    {
        $this->permissions()->sync($permissionIds);
    }
}
