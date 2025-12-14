<?php

namespace App\Core\Repositories\Eloquent;

use App\Core\Repositories\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

abstract class BaseRepository implements RepositoryInterface
{
    protected Model $model;
    protected $query;

    public function __construct()
    {
        $this->model = $this->makeModel();
        $this->query = $this->model->newQuery();
    }

    /**
     * Specify Model class name
     */
    abstract protected function model(): string;

    /**
     * Make Model instance
     */
    protected function makeModel(): Model
    {
        $model = app($this->model());

        if (!$model instanceof Model) {
            throw new \Exception("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        return $model;
    }

    /**
     * Reset query
     */
    protected function resetQuery(): void
    {
        $this->query = $this->model->newQuery();
    }

    public function all(array $columns = ['*']): Collection
    {
        $result = $this->query->get($columns);
        $this->resetQuery();
        return $result;
    }

    public function find(int $id, array $columns = ['*']): ?Model
    {
        $result = $this->query->find($id, $columns);
        $this->resetQuery();
        return $result;
    }

    public function findOrFail(int $id, array $columns = ['*']): Model
    {
        $result = $this->query->findOrFail($id, $columns);
        $this->resetQuery();
        return $result;
    }

    public function findBy(string $attribute, mixed $value, array $columns = ['*']): ?Model
    {
        $result = $this->query->where($attribute, $value)->first($columns);
        $this->resetQuery();
        return $result;
    }

    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        $result = $this->query->paginate($perPage, $columns);
        $this->resetQuery();
        return $result;
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Model
    {
        $record = $this->findOrFail($id);
        $record->update($data);
        return $record->fresh();
    }

    public function delete(int $id): bool
    {
        $record = $this->findOrFail($id);
        return $record->delete();
    }

    public function with(array|string $relations): self
    {
        $this->query = $this->query->with($relations);
        return $this;
    }

    public function where(string $column, mixed $value): self
    {
        $this->query = $this->query->where($column, $value);
        return $this;
    }

    public function orderBy(string $column, string $direction = 'asc'): self
    {
        $this->query = $this->query->orderBy($column, $direction);
        return $this;
    }

    /**
     * Get query builder
     */
    public function getQuery()
    {
        return $this->query;
    }
}
