<?php

namespace Maxxidev\ApiDispatcher;

abstract class Response
{
    public $baseResponse;

    public function __construct($response)
    {
        $this->baseResponse = $response;
    }

    public static function fromBaseResponse($response)
    {
        return new static($response);
    }

    abstract public function raw();

    public function json(...$options)
    {
        return json_decode($this->raw(), ...$options);
    }

    public function __get($key)
    {
        return $this->baseResponse->{$key};
    }

    public function __isset($key)
    {
        return isset($this->baseResponse->{$key});
    }

    public function __call($method, $args)
    {
        return $this->baseResponse->{$method}(...$args);
    }
}
