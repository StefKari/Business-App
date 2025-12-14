<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine if the given user can view any users.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('users.view');
    }

    /**
     * Determine if the given user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // SysAdmin can view all
        if ($user->isSysAdmin()) {
            return true;
        }

        // Users can view themselves
        if ($user->id === $model->id) {
            return true;
        }

        // Users can view users they created
        if ($model->created_by === $user->id) {
            return true;
        }

        return $user->hasPermission('users.view');
    }

    /**
     * Determine if the given user can create users.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('users.create');
    }

    /**
     * Determine if the given user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // SysAdmin can update all
        if ($user->isSysAdmin()) {
            return true;
        }

        // Users can update themselves (limited fields)
        if ($user->id === $model->id) {
            return true;
        }

        // Can update users they created (if they have permission)
        if ($model->created_by === $user->id && $user->hasPermission('users.update')) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the given user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Cannot delete yourself
        if ($user->id === $model->id) {
            return false;
        }

        // SysAdmin can delete anyone
        if ($user->isSysAdmin()) {
            return true;
        }

        // Can delete users they created
        if ($model->created_by === $user->id && $user->hasPermission('users.delete')) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the given user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->isSysAdmin() || $user->hasPermission('users.restore');
    }

    /**
     * Determine if the given user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->isSysAdmin();
    }

    /**
     * Determine if user can change roles
     */
    public function changeRole(User $user, User $model): bool
    {
        // Cannot change your own role
        if ($user->id === $model->id) {
            return false;
        }

        // Only SysAdmin can change roles
        return $user->isSysAdmin();
    }
}
