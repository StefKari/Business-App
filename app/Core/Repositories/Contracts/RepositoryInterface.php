<?php

namespace App\Core\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface RepositoryInterface
{
    /**
     * Get all records
     */
    public function all(array $columns = ['*']): Collection;

    /**
     * Find record by ID
     */
    public function find(int $id, array $columns = ['*']): ?Model;

    /**
     * Find record by ID or fail
     */
    public function findOrFail(int $id, array $columns = ['*']): Model;

    /**
     * Find by attribute
     */
    public function findBy(string $attribute, mixed $value, array $columns = ['*']): ?Model;

    /**
     * Get paginated records
     */
    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator;

    /**
     * Create new record
     */
    public function create(array $data): Model;

    /**
     * Update record
     */
    public function update(int $id, array $data): Model;

    /**
     * Delete record
     */
    public function delete(int $id): bool;

    /**
     * Get records with relations
     */
    public function with(array|string $relations): self;

    /**
     * Apply where clause
     */
    public function where(string $column, mixed $value): self;

    /**
     * Apply orderBy clause
     */
    public function orderBy(string $column, string $direction = 'asc'): self;
}
