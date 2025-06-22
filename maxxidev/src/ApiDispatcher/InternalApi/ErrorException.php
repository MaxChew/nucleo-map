<?php

namespace Maxxidev\ApiDispatcher\InternalApi;

use Maxxidev\ApiDispatcher\ApiErrorException;

class ErrorException extends ApiErrorException
{
    public $response;

    public function __construct($response)
    {
        $this->response = $response;
        parent::__construct(implode(' ', [
            "Internal request failed with status code {$response->status()}.",
            $response->exception ? '['.get_class($response->exception).']' : null,
            $response->exception ? $response->exception->getMessage() : (string) $response,
        ]));
    }

    public function getResponse()
    {
        return Response::fromBaseResponse($this->response);
    }
}
