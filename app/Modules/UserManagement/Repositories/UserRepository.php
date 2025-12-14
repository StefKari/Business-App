<?php

namespace App\Modules\UserManagement\Repositories;

use App\Core\Repositories\Eloquent\BaseRepository;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository extends BaseRepository
{
    protected function model(): string
    {
        return User::class;
    }

    /**
     * Get active users
     */
    public function getActive(): Collection
    {
        return $this->query->where('is_active', true)->get();
    }

    /**
     * Get users by role
     */
    public function getByRole(string $roleSlug): Collection
    {
        return $this->query->whereHas('role', function ($q) use ($roleSlug) {
            $q->where('slug', $roleSlug);
        })->get();
    }

    /**
     * Get users created by specific user
     */
    public function getCreatedBy(int $userId): Collection
    {
        return $this->query->where('created_by', $userId)->get();
    }

    /**
     * Search users
     */
    public function search(string $query)
    {
        return $this->query->where(function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
              ->orWhere('email', 'like', "%{$query}%");
        });
    }

    /**
     * Get users with role
     */
    public function getAllWithRole()
    {
        return $this->query->with('role')->get();
    }
}
