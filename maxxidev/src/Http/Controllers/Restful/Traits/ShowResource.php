<?php

namespace Maxxidev\Http\Controllers\Restful\Traits;

use Illuminate\Http\Request;

trait ShowResource
{
    protected $key;

    public function show(Request $request)
    {
        return $this->success(null, $this->transformShowData($this->routeModel()));
    }

    protected function routeModel($paramKey = null)
    {
        $routeKey = $paramKey ?
            $this->request->route()->parameter($paramKey) :
            last($this->request->route()->parameters());

        return $this->key ?
            $this->repository->findBy($this->key, $routeKey) :
            $this->repository->find($routeKey);
    }

    protected function transformShowData($data)
    {
        return $this->transformData($data);
    }
}
