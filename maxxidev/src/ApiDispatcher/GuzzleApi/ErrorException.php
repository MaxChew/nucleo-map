<?php

namespace Maxxidev\ApiDispatcher\GuzzleApi;

use Maxxidev\ApiDispatcher\ApiErrorException;

class ErrorException extends ApiErrorException
{
    public $baseException;

    public function __construct($exception)
    {
        $this->baseException = $exception;
        parent::__construct($exception->getMessage());
    }

    public function getResponse()
    {
        return Response::fromBaseResponse($this->baseException->getResponse());
    }
}
