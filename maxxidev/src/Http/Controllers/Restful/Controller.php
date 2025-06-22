<?php

namespace Maxxidev\Http\Controllers\Restful;

use App\Http\Controllers\Controller as BaseController;
use Maxxidev\Http\Controllers\Restful\RequestParsers\QueryStringToEloquent;

abstract class Controller extends BaseController
{
    use Traits\ListResource,
        Traits\ShowResource;

    protected $repository;

    protected $requestParser;

    abstract protected function source();

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->request = $request;
            $this->repository = $this->resolveRepository();
            $this->requestParser = $this->resolveRequestParser();

            $this->registerCustomFilters();
            $this->registerCustomSorts();

            return $next($request);
        });
    }

    protected function resolveRepository()
    {
        $source = $this->source();

        return is_object($source) ? $source : app($source);
    }

    protected function resolveRequestParser()
    {
        return new QueryStringToEloquent($this->request);
    }

    protected function transformData($data)
    {
        return $data;
    }

    protected function transformCollection($collection)
    {
        return $collection;
    }

    protected function registerCustomFilters()
    {
        // $this->requestParser->registerCustomFilter('status', function () {})
    }

    protected function registerCustomSorts()
    {
        // $this->requestParser->registerCustomSort('status', function () {})
    }
}
