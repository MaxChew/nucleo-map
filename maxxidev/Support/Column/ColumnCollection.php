<?php

namespace Maxxidev\Support\Column;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ColumnCollection extends Collection
{
    public static function load(array $columns)
    {
        return new static(
            collect($columns)->map(function ($options, $column) {
                if (is_string($options)) {
                    $column = $options;
                    $options = [];
                }
                return static::resolve($column, $options);
            })
        );
    }

    protected static function resolve($column, array $options = [])
    {
        $type = Str::studly($options['type'] ?? 'text');

        $class = collect([
            __NAMESPACE__ . '\\' . $type,
            __NAMESPACE__ . '\\Text',
        ])->first(function ($class) {
            return class_exists($class);
        });

        return new $class($column, $options);
    }

}