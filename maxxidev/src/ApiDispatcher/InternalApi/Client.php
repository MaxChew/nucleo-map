<?php

namespace Maxxidev\ApiDispatcher\InternalApi;

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Maxxidev\ApiDispatcher\ApiClient;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Client implements ApiClient
{
    protected $app;

    protected $router;

    protected $request;

    protected $data = [];

    protected $headers = [];

    protected $cookies = [];

    protected $uploads = [];

    protected $disableMiddleware = false;

    public function __construct(Application $app, Request $request, Router $router)
    {
        $this->app = $app;
        $this->request = $request;
        $this->router = $router;
    }

    public function sendRequest($method, $uri, array $data = [], array $headers = [])
    {
        return $this->singleRequest($method, $uri, $data, $headers);
    }

    public function with($data)
    {
        $this->data = array_merge($this->data, is_array($data) ? $data : func_get_args());

        return $this;
    }

    public function header($key, $value)
    {
        $this->headers[$key] = $value;

        return $this;
    }

    public function cookie(Cookie $cookie)
    {
        $this->cookies[] = $cookie;

        return $this;
    }

    public function attach(array $files)
    {
        foreach ($files as $key => $file) {
            if (is_array($file)) {
                $file = new UploadedFile($file['path'], basename($file['path']), $file['mime'], $file['size']);
            } elseif (is_string($file)) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);

                $file = new UploadedFile($file, basename($file), finfo_file($finfo, $file), $this->files->size($file));
            } elseif (! $file instanceof UploadedFile) {
                continue;
            }

            $this->uploads[$key] = $file;
        }

        return $this;
    }

    /**
     * @param  array  $requests An array of requests
     * @return array
     */
    public function batchRequest(array $requests)
    {
        foreach ($requests as $i => $request) {
            $requests[$i] = call_user_func_array([$this, 'singleRequest'], $request);
        }

        return $requests;
    }

    /**
     * @param  string  $method
     * @param  string  $uri
     * @param  array  $data
     * @param  array  $headers
     * @param  string  $content
     * @return \Illuminate\Http\Response
     */
    public function singleRequest($method, $uri, array $data = [], array $headers = [], $content = null)
    {
        // Save the current request so we can reset the router back to it
        // after we've completed our internal request.
        $currentRequest = $this->request->instance()->duplicate();

        $headers = $this->overrideHeaders($currentRequest->server->getHeaders(), $headers);

        if ($this->disableMiddleware) {
            $this->app->instance('middleware.disable', true);
        }

        $response = $this->request($method, $uri, $data, $headers, $content);

        if ($this->disableMiddleware) {
            $this->app->instance('middleware.disable', false);
        }

        // Once the request has completed we reset the currentRequest of the router
        // to match the original request.
        $this->request->instance()->initialize(
            $currentRequest->query->all(),
            $currentRequest->request->all(),
            $currentRequest->attributes->all(),
            $currentRequest->cookies->all(),
            $currentRequest->files->all(),
            $currentRequest->server->all(),
            $currentRequest->content
        );

        return $response;
    }

    protected function overrideHeaders(array $default, array $headers)
    {
        $headers = $this->transformHeadersToUppercaseUnderscoreType($headers);

        return array_merge($default, $headers);
    }

    public function enableMiddleware()
    {
        $this->disableMiddleware = false;
    }

    public function disableMiddleware()
    {
        $this->disableMiddleware = true;
    }

    /**
     * @param  string  $method
     * @param  string  $uri
     * @param  array  $data
     * @param  array  $headers
     * @param  string  $content
     * @return \Illuminate\Http\Response
     */
    protected function request($method, $uri, array $data = [], array $headers = [], $content = null)
    {
        // Create a new request object for the internal request
        $request = $this->createRequest($method, $uri, $data, $headers, $content);

        // Handle the request in the kernel and prepare a response
        $response = $this->router->dispatch($request);

        if (! $response->isSuccessful() && ! $response->isRedirection()) {
            throw new ErrorException($response);
        }

        return Response::fromBaseResponse($response);
    }

    /**
     * @param  string  $method
     * @param  string  $uri
     * @param  array  $data
     * @param  array  $headers
     * @param  string  $content
     * @return \Illuminate\Http\Request
     */
    protected function createRequest($method, $uri, array $data = [], array $headers = [], $content = null)
    {
        $data = array_merge($this->data, $data);
        $headers = array_merge($this->headers, $headers);

        $server = $this->transformHeadersToServerVariables($headers);

        return $this->request->create($uri, $method, $data, $this->cookies, $this->uploads, $server, $content);
    }

    protected function transformHeadersToUppercaseUnderscoreType($headers)
    {
        $transformed = [];

        foreach ($headers as $headerType => $headerValue) {
            $headerType = strtoupper(str_replace('-', '_', $headerType));

            $transformed[$headerType] = $headerValue;
        }

        return $transformed;
    }

    /**
     * https://github.com/symfony/symfony/issues/5074
     *
     * @param  array  $headers
     * @return array
     */
    protected function transformHeadersToServerVariables($headers)
    {
        $server = [];

        foreach ($headers as $headerType => $headerValue) {
            $headerType = 'HTTP_'.$headerType;

            $server[$headerType] = $headerValue;
        }

        return $server;
    }
}
