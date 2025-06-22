<?php

namespace Maxxidev\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class EloquentRepository extends AbstractRepository
{
    protected $builder;

    public static function of($model)
    {
        if (is_string($model)) {
            $model = app($model);
        }

        if ($model instanceof Relation) {
            $model = $model->getQuery();
        }

        return new static(($model instanceof Model) ? $model->query() : $model);
    }

    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    public function get()
    {
        return $this->builder->get();
    }

    public function paginate($perPage = null, $page = null)
    {
        return $this->builder->paginate($perPage, ['*'], 'page', $page);
    }

    public function find($id)
    {
        return (clone $this->builder)->findOrFail($id);
    }

    public function findBy($attribute, $value)
    {
        return (clone $this->builder)->where($attribute, $value)->firstOrFail();
    }

    public function findByAttributes(array $attributes)
    {
        return (clone $this->builder)->where($attributes)->firstOrFail();
    }

    public function create(array $attributes)
    {
        return $this->builder->create($attributes);
    }

    public function update($model, array $attributes)
    {
        $model = ($model instanceof Model) ? $model : $this->find($model);

        return $model->update($attributes);
    }

    public function delete($model)
    {
        $model = ($model instanceof Model) ? $model : $this->find($model);

        return $model->delete();
    }

    public function addScope($callable)
    {
        app()->call($callable, [$this->builder]);

        return $this;
    }

    public function getBuilder()
    {
        return $this->builder;
    }
}
