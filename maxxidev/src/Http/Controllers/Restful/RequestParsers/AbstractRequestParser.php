<?php

namespace Maxxidev\Http\Controllers\Restful\RequestParsers;

use Illuminate\Http\Request;
use Maxxidev\Repositories\Criteria\CustomFilter;
use Maxxidev\Repositories\Criteria\CustomSort;

abstract class AbstractRequestParser
{
    protected $request;

    protected $customSorts = [];

    protected $customFilters = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getFilterCriterias()
    {
        return collect($this->extractFilters())->map(function ($rawValue, $attribute) {
            if (isset($this->customFilters[$attribute])) {
                return new CustomFilter($attribute, $rawValue, $this->customFilters[$attribute]);
            }

            return $this->parseFilterCriteria($attribute, $rawValue);
        })->all();
    }

    public function getSortCriterias()
    {
        return collect($this->extractSorts())->map(function ($rawValue, $attribute) {
            if (isset($this->customSorts[$attribute])) {
                return new CustomSort($attribute, $rawValue, $this->customSorts[$attribute]);
            }

            return $this->parseSortCriteria($attribute, $rawValue);
        })->all();
    }

    public function registerCustomFilter($attribute, $callable)
    {
        $this->customFilters[$attribute] = $callable;

        return $this;
    }

    public function registerCustomSort($attribute, $callable)
    {
        $this->customSorts[$attribute] = $callable;

        return $this;
    }

    abstract public function getPerPage();

    abstract protected function extractFilters();

    abstract protected function extractSorts();

    abstract protected function parseFilterCriteria($attribute, $raw);

    abstract protected function parseSortCriteria($attribute, $raw);
}
