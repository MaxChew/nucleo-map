<?php

namespace Maxxidev\ApiDispatcher;

interface ApiClient
{
    public function sendRequest($method, $uri, array $data = [], array $headers = []);
}
