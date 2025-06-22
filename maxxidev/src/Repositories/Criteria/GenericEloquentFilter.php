<?php

namespace Maxxidev\Repositories\Criteria;

use Illuminate\Support\Carbon;

class GenericEloquentFilter implements CriteriaInterface
{
    protected $type;

    protected $attribute;

    protected $operator;

    protected $value;

    public function __construct($type, $attribute, $operator, $value)
    {
        $this->type = $type;
        $this->attribute = $attribute;
        $this->operator = $operator;
        $this->value = $value;
    }

    public function apply($builder)
    {
        $this->applyWhereClause($builder, $this->attribute);
    }

    protected function applyWhereClause($builder, $attribute)
    {
        if (is_string($attribute) && strpos($attribute, '.') !== false) {
            [$relation, $attribute] = explode('.', $attribute, 2);
            $builder->whereHas($relation, function ($related) use ($attribute) {
                return $this->applyWhereClause($related, $attribute);
            });

            return;
        }

        if ($this->type) {
            if ($this->type === 'Between' && is_array($this->value)) {
                try {
                    $startDate = Carbon::createFromFormat('Y-m-d', $this->value[0]);
                    $endDate = Carbon::createFromFormat('Y-m-d', $this->value[1]);
                    $builder->where($attribute, '>=', $startDate->startOfDay())
                        ->where($attribute, '<', $endDate->addDay()->startOfDay());

                    return;
                } catch (\InvalidArgumentException $e) {
                    // not date
                }
            }
            $builder->{'where'.$this->type}($attribute, $this->value);
        } else {
            if ($this->operator === null) {
                $builder->where($attribute, $this->value);
            } else {
                $builder->where($attribute, $this->operator, $this->value);
            }
        }
    }
}
