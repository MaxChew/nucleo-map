<?php

namespace Maxxidev\ApiDispatcher;

abstract class ApiErrorException extends ApiException
{
    abstract public function getResponse();

    public function getResponseData()
    {
        return $this->getResponse()->json();
    }

    public function getResponseRaw()
    {
        return $this->getResponse()->raw();
    }
}
