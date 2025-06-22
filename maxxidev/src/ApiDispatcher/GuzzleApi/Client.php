<?php

namespace Maxxidev\ApiDispatcher\GuzzleApi;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\ClientException as GuzzleClientException;
use GuzzleHttp\Exception\GuzzleException;
use Maxxidev\ApiDispatcher\ApiClient;

class Client implements ApiClient
{
    protected $configs;

    public function __construct($configs = [])
    {
        $this->configs = array_replace_recursive([
            'http_errors' => true,
            'timeout' => 30,
            'headers' => [
                'Accept' => 'application/json',
                'X-Requested-With' => 'XMLHttpRequest',
            ],
        ], $configs);
    }

    public function config($key, $value = null)
    {
        if (is_array($key)) {
            $this->configs = array_replace_recursive($this->configs, $key);
        } else {
            array_set($this->configs, $key, $value);
        }

        return $this;
    }

    public function sendRequest($method, $uri, array $data = [], array $headers = [])
    {
        $configs = array_replace_recursive($this->configs, [
            'headers' => $headers,
            (strtoupper($method) === 'GET') ? 'query' : 'json' => $data,
        ]);
        try {
            $response = (new Guzzle($configs))->request($method, $uri);

            return Response::fromBaseResponse($response);
        } catch(GuzzleClientException $e) {
            throw new ErrorException($e);
        } catch (GuzzleException $e) {
            throw new NetworkException($e);
        }
    }
}
