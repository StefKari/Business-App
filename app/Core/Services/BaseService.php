<?php

namespace App\Core\Services;

use App\Core\Repositories\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

abstract class BaseService
{
    protected RepositoryInterface $repository;

    /**
     * Get all records
     */
    public function getAll(array $columns = ['*']): Collection
    {
        return $this->repository->all($columns);
    }

    /**
     * Get paginated records
     */
    public function getPaginated(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $columns);
    }

    /**
     * Find record by ID
     */
    public function findById(int $id, array $columns = ['*']): ?Model
    {
        return $this->repository->find($id, $columns);
    }

    /**
     * Find record by ID or fail
     */
    public function findByIdOrFail(int $id, array $columns = ['*']): Model
    {
        return $this->repository->findOrFail($id, $columns);
    }

    /**
     * Create new record
     */
    public function create(array $data): Model
    {
        return $this->repository->create($data);
    }

    /**
     * Update record
     */
    public function update(int $id, array $data): Model
    {
        return $this->repository->update($id, $data);
    }

    /**
     * Delete record
     */
    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    /**
     * Find by attribute
     */
    public function findBy(string $attribute, mixed $value, array $columns = ['*']): ?Model
    {
        return $this->repository->findBy($attribute, $value, $columns);
    }
}
