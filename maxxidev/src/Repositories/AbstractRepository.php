<?php

namespace Maxxidev\Repositories;

use Maxxidev\Repositories\Criteria\CriteriaInterface as Criteria;

abstract class AbstractRepository
{
    protected $aggregations = [];

    public function applyFilterCriterias($criterias)
    {
        foreach ($criterias as $name => $criteria) {
            $this->applyFilterCriteria($criteria);
        }

        return $this;
    }

    public function applySortCriterias($criterias)
    {
        foreach ($criterias as $name => $criteria) {
            $this->applySortCriteria($criteria);
        }

        return $this;
    }

    public function applyFilterCriteria(Criteria $criteria)
    {
        $criteria->apply($this->builder);

        return $this;
    }

    public function applySortCriteria(Criteria $criteria)
    {
        $criteria->apply($this->builder);

        return $this;
    }

    abstract public function get();

    abstract public function paginate($perPage = null, $page = null);

    abstract public function find($id);

    abstract public function findBy($attribute, $value);

    abstract public function findByAttributes(array $attributes);
}
