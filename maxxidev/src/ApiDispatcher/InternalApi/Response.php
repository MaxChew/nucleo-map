<?php

namespace Maxxidev\ApiDispatcher\InternalApi;

use Illuminate\Http\JsonResponse;
use Maxxidev\ApiDispatcher\Response as ApiResponse;

class Response extends ApiResponse
{
    public function raw()
    {
        return $this->baseResponse->getContent();
    }

    public function json(...$options)
    {
        if ($this->baseResponse instanceof JsonResponse) {
            return $this->baseResponse->getData(...$options);
        }

        return json_decode($this->baseResponse->getContent(), ...$options);
    }
}
