<?php

namespace Maxxidev\Repositories\Criteria;

class CustomSort implements CriteriaInterface
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
        $name = 'sort-'.$this->attribute;
        app()->bind($name, $this->callable);

        return resolve($name, [$builder, $this->value, $this->attribute]);

        // return app()->call($this->callable, [$builder, $this->value, $this->attribute]);
    }
}
