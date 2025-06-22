<?php

namespace Maxxidev\ApiDispatcher\GuzzleApi;

use Maxxidev\ApiDispatcher\Response as ApiResponse;

class Response extends ApiResponse
{
    public function raw()
    {
        return $this->baseResponse->getBody();
    }
}
