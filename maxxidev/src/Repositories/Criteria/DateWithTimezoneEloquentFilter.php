<?php

namespace Maxxidev\Repositories\Criteria;

use Illuminate\Support\Facades\DB;

class DateWithTimezoneEloquentFilter extends GenericEloquentFilter
{
    protected $toTimezone;

    protected $fromTimezone;

    public function __construct($type, $attribute, $operator, $value, $toTimezone, $fromTimezone = null)
    {
        $this->type = $type;
        $this->attribute = $attribute;
        $this->operator = $operator;
        $this->value = $value;
        $this->toTimezone = now($toTimezone)->format('P');
        $this->fromTimezone = now($fromTimezone ?? config('app.timezone'))->format('P');
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

        $attribute = DB::raw("convert_tz({$attribute}, '{$this->fromTimezone}', '{$this->toTimezone}')");

        parent::applyWhereClause($builder, $attribute);
    }
}
