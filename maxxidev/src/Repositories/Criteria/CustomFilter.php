<?php

namespace Maxxidev\Repositories\Criteria;

class CustomFilter implements CriteriaInterface
{
    protected $attribute;

    protected $value;

    protected $callable;

    public function __construct($attribute, $value, $callable)
    {
        $this->attribute = $attribute;
        $this->value = $value;
        $this->callable = $callable;
    }

    public function apply($builder)
    {
        $name = 'filter-'.$this->attribute;
        app()->bind($name, $this->callable);

        return resolve($name, [$builder, $this->value, $this->attribute]);

        // return app()->call($this->callable, [$builder, $this->value, $this->attribute]);
    }
}
