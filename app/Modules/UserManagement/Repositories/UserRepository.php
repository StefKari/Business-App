<?php

namespace App\Modules\UserManagement\Repositories;

use App\Core\Repositories\Eloquent\BaseRepository;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class UserRepository extends BaseRepository
{
    protected function model(): string
    {
        return User::class;
    }

    public function getActive(): Collection
    {
        $result = $this->query->where('is_active', true)->get();
        $this->resetQuery();
        return $result;
    }

    public function getByRole(string $roleSlug): Collection
    {
        $result = $this->query->whereHas('role', function ($q) use ($roleSlug) {
            $q->where('slug', $roleSlug);
        })->get();
        $this->resetQuery();
        return $result;
    }

    public function getCreatedBy(int $userId): Collection
    {
        $result = $this->query->where('created_by', $userId)->get();
        $this->resetQuery();
        return $result;
    }

    public function search(string $search): Collection
    {
        $result = $this->query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        })->with('role')->get();
        $this->resetQuery();
        return $result;
    }

    public function getAllWithRole(): Collection
    {
        $result = $this->query->with('role')->get();
        $this->resetQuery();
        return $result;
    }

    public function getAllWithRolePaginated(?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->query->with('role')->orderBy('created_at', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $result = $query->paginate($perPage);
        $this->resetQuery();
        return $result;
    }
}
