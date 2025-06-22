<?php

namespace Maxxidev\Http\Controllers\Restful\Traits;

trait ListResource
{
    // use false or 0 to show all
    // null will be defaulted to model->perPage
    protected $perPage;

    public function index()
    {
        $repository = $this->applyCriteriasFromRequest();

        $perPage = $this->requestParser->getPerPage() ?? $this->perPage;

        $data = ($perPage !== null && $perPage == false) ?
            $repository->get() :
            $repository->paginate($perPage);

        return $this->success(null, $this->transformIndexData($data));
    }

    protected function applyCriteriasFromRequest()
    {
        return $this->repository
            ->applyFilterCriterias($this->requestParser->getFilterCriterias())
            ->applySortCriterias($this->requestParser->getSortCriterias());
    }

    protected function transformIndexData($data)
    {
        return $this->transformCollection($data);
    }
}
