<?php

namespace Maxxidev\Http\Controllers\Restful;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\JsonResponse as BaseJsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Arr;

class JsonResponse extends BaseJsonResponse
{
    public static function success($message = null, $data = null, $statusCode = 200)
    {
        return (new static([
            'status' => 'success',
            'message' => $message,
        ], $statusCode))->withData($data);
    }

    public static function error($message = null, $data = null, $statusCode = 412)
    {
        return (new static([
            'status' => 'fail',
            'message' => $message,
        ], $statusCode))->withData($data);
    }

    public static function fail($message = null, $data = null, $statusCode = 412)
    {
        return (new static([
            'status' => 'fail',
            'message' => $message,
        ], $statusCode))->withData($data);
    }

    public function withData($data)
    {
        if ($data instanceof Paginator) {
            $data = JsonResource::collection($data);
        }

        if (($data instanceof JsonResource) ||
            ($data instanceof ResourceCollection)) {
            $existing = Arr::except((array) $this->getData(), 'data');

            $response = $data->additional($existing)->response();

            return tap($this)->setData($response->getData());
        }

        $result = $this->getData();
        $result->data = $data;

        return tap($this)->setData($result);
    }

    public function withMeta($key, $value = null)
    {
        $result = $this->getData();

        if (! is_array($key)) {
            $key = [$key => $value];
        }

        $result->meta = array_merge((array) object_get($result, 'meta'), $key);

        return tap($this)->setData($result);
    }
}
