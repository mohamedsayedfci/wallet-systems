<?php

namespace App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

/**
 * Abstract BaseRepository class.
 *
 * Provides common data access methods for all repositories.
 * Ensures consistency and reduces code duplication.
 */
abstract class BaseRepository
{
    /**
     * The model instance.
     */
    protected Model $model;

    /**
     * Create class instance.
     *
     *
     * @return void
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Retrieve all models.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return $this->model::all();
    }

    /**
     * Paginate models.
     */
    public function paginate(array $queryParams, int $perPage = 10, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->model->paginate($perPage, $columns);
    }

    /**
     * Find a model by its ID.
     *
     *
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findById(int $id, array $columns = ['*']): Model
    {
        return $this->model->findOrFail($id, $columns);
    }

    /**
     * Update a model by its ID.
     */
    public function updateById(int $id, array $attributes): void
    {
        $this->model->where('id', $id)->update($attributes);
    }

    /**
     * Create a new model instance.
     */
    public function create(array $attributes): Model
    {
        return $this->model->create($attributes);
    }

    /**
     * Update or create a model.
     */
    public function updateOrCreate(array $attributes, array $values): Model
    {
        return $this->model->updateOrCreate($attributes, $values);
    }

    /**
     * Delete a model by its ID.
     */
    public function deleteById(int $id): void
    {
        $this->model->where('id', $id)->delete();
    }
}
