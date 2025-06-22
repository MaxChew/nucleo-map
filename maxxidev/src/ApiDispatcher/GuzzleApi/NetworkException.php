<?php

namespace Maxxidev\ApiDispatcher\GuzzleApi;

use Maxxidev\ApiDispatcher\ApiNetworkException;

class NetworkException extends ApiNetworkException
{
    public $baseException;

    public function __construct($exception)
    {
        $this->baseException = $exception;
        parent::__construct($exception->getMessage());
    }

    public function getResponse()
    {
        return $this->baseException->getResponse();
    }
}
