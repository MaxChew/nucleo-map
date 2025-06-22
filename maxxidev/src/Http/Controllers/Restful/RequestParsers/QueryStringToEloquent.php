<?php

namespace Maxxidev\Http\Controllers\Restful\RequestParsers;

use Illuminate\Support\Str;
use Maxxidev\Repositories\Criteria\GenericEloquentFilter;
use Maxxidev\Repositories\Criteria\GenericEloquentSort;

class QueryStringToEloquent extends AbstractRequestParser
{
    public function getPerPage()
    {
        return $this->request->query('per_page');
    }

    protected function extractFilters()
    {
        return $this->request->query('filters', []);
    }

    protected function extractSorts()
    {
        return array_reduce($this->request->query('sorts', []), function ($sorts, $raw) {
            $parts = explode(':', $raw, 2);
            $sorts[$parts[0]] = $parts[1] ?? null;

            return $sorts;
        }, []);
    }

    // follow https://www.npmjs.com/package/mongo-querystring convention
    // wrap query string value with `...` to escape, enforce literal
    protected function parseFilterCriteria($attribute, $raw)
    {
        extract(static::decodeFilter($raw));

        return new GenericEloquentFilter($type, $attribute, $operator, $value);
    }

    // for sorting, use gender:asc or first_name:desc
    // eg. sorts = ['gender:asc', 'first_name:desc']
    protected function parseSortCriteria($attribute, $raw)
    {
        return new GenericEloquentSort($attribute, $raw ?? 'asc');
    }

    public static function decodeFilter($raw)
    {
        $type = $value = $operator = null;

        if (is_array($raw)) {
            $type = 'In';
            $type = collect($raw)->every(function ($value) {
                return Str::startsWith($value, '!');
            }) ? 'NotIn' : 'In';
            $value = $type === 'NotIn' ? array_map(function ($value) {
                return (string) Str::substr($value, 1);
            }, $raw) : $raw;
        } else {
            if (strlen($raw) === 0) {
                $value = null;
                $operator = '!=';
            } elseif (Str::is('*|*', $raw) && ! Str::is('`*`', $raw)) {
                [$from, $to] = explode('|', $raw, 2);
                if (strlen($from) === 0) {
                    $operator = '<=';
                    $value = $to;
                } elseif (strlen($to) === 0) {
                    $operator = '>=';
                    $value = $from;
                } else {
                    $type = 'Between';
                    $value = [$from, $to];
                }
            } else {
                if (Str::startsWith($raw, ['>=', '<='])) {
                    $operator = Str::substr($raw, 0, 2);
                    $value = (string) Str::substr($raw, 2);
                } else {
                    $prefix = Str::substr($raw, 0, 1);
                    $remain = (string) Str::substr($raw, 1);
                    switch ($prefix) {
                        case '!':
                            if (strlen($remain) === 0) {
                                $value = null;
                            } else {
                                $operator = '!=';
                                $value = $remain;
                            }
                            break;
                        case '>':
                        case '<':
                            $operator = $prefix;
                            $value = $remain;
                            break;
                        case '^':
                            $operator = 'like';
                            $value = $remain.'%';
                            break;
                        case '$':
                            $operator = 'like';
                            $value = '%'.$remain;
                            break;
                        case '~':
                            $operator = 'like';
                            $value = '%'.$remain.'%';
                            break;
                        default:
                            $operator = '=';
                            $value = $raw;
                            break;
                    }
                }
            }
        }

        $value = static::trimEscape($value);

        return compact('type', 'value', 'operator');
    }

    public static function trimEscape($value)
    {
        if ($value === null) {
            return $value;
        }

        return is_array($value) ? array_map(function ($val) {
            return trim($val, '`');
        }, $value) : trim($value, '`');
    }
}
