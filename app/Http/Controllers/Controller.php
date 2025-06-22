<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Maxxidev\Http\Controllers\Restful\JsonResponse;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    protected function domain()
    {
        return app('domain');
    }

    public function success($message = null, $data = null, $statusCode = 200)
    {
        return JsonResponse::success($message, $data, $statusCode);
    }

    public function error($message = null, $data = null, $statusCode = 412)
    {
        return JsonResponse::fail($message, $data, $statusCode);
    }

    public function fail($message = null, $data = null, $statusCode = 412)
    {
        return JsonResponse::fail($message, $data, $statusCode);
    }

    protected function message($key = '', $model = 'Model')
    {
        return __('api.restful.'.$key, ['model' => $model]);
    }
}
