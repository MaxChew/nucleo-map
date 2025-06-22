<?php

namespace Maxxidev\Repositories\Criteria;

class GenericEloquentSort implements CriteriaInterface
{
    protected $attribute;

    protected $direction;

    public function __construct($attribute, $direction)
    {
        $this->attribute = $attribute;
        $this->direction = $direction;
    }

    public function apply($builder)
    {
        $builder->orderBy($this->attribute, $this->direction);
    }
}
