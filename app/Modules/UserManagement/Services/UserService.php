<?php

namespace App\Modules\UserManagement\Services;

use App\Core\Services\BaseService;
use App\Models\User;
use App\Modules\UserManagement\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

class UserService extends BaseService
{
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Create new user
     */
    public function createUser(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        $data['created_by'] = auth()->id();

        return $this->repository->create($data);
    }

    /**
     * Update user
     */
    public function updateUser(int $id, array $data): User
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        return $this->repository->update($id, $data);
    }

    /**
     * Get active users
     */
    public function getActiveUsers(): Collection
    {
        return $this->repository->getActive();
    }

    /**
     * Get users by role
     */
    public function getUsersByRole(string $roleSlug): Collection
    {
        return $this->repository->getByRole($roleSlug);
    }

    /**
     * Get users created by current user
     */
    public function getMyCreatedUsers(): Collection
    {
        return $this->repository->getCreatedBy(auth()->id());
    }

    /**
     * Activate user
     */
    public function activateUser(int $id): User
    {
        return $this->repository->update($id, ['is_active' => true]);
    }

    /**
     * Deactivate user
     */
    public function deactivateUser(int $id): User
    {
        return $this->repository->update($id, ['is_active' => false]);
    }

    /**
     * Change user role
     */
    public function changeUserRole(int $userId, int $roleId): User
    {
        return $this->repository->update($userId, ['role_id' => $roleId]);
    }

    /**
     * Search users
     */
    public function searchUsers(string $query)
    {
        return $this->repository->search($query)->all();
    }

    /**
     * Get all users with their roles (paginated + optional search)
     */
    public function getUsersWithRoles(?string $search = null, int $perPage = 15)
    {
        return $this->repository->getAllWithRolePaginated($search, $perPage);
    }

    /**
     * Check if user can be created by current user
     */
    public function canCreateUser(int $roleId): bool
    {
        $currentUser = auth()->user();

        // SysAdmin can create anyone
        if ($currentUser->isSysAdmin()) {
            return true;
        }

        // Admin can create moderators only
        if ($currentUser->isAdmin()) {
            $role = \App\Models\Role::find($roleId);
            return $role && $role->isModerator();
        }

        return false;
    }
}
