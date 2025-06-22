<?php

namespace Maxxidev\ApiDispatcher;

class Dispatcher
{
    protected $client;

    public function __construct(ApiClient $client)
    {
        $this->client = $client;
    }

    public function __call($method, $args)
    {
        $this->client->{$method}(...$args);

        return $this;
    }

    public function get($uri, array $params = [], array $headers = [])
    {
        return $this->client->sendRequest('GET', $uri, $params, $headers);
    }

    public function post($uri, array $data = [], array $headers = [])
    {
        return $this->client->sendRequest('POST', $uri, $data, $headers);
    }

    public function patch($uri, array $data = [], array $headers = [])
    {
        return $this->client->sendRequest('PATCH', $uri, $data, $headers);
    }

    public function put($uri, array $data = [], array $headers = [])
    {
        return $this->client->sendRequest('PUT', $uri, $data, $headers);
    }

    public function destroy($uri, array $data = [], array $headers = [])
    {
        return $this->client->sendRequest('DELETE', $uri, $data, $headers);
    }
}
