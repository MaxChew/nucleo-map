<?php

namespace Maxxidev\ApiDispatcher\InternalApi;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router as BaseRouter;
use Illuminate\Support\Str;

class Router extends BaseRouter
{
    protected $baseRouter;

    protected $skipMiddlewares = [
        "Illuminate\Routing\Middleware\ThrottleRequests*",
        "Illuminate\Auth\Middleware\Authenticate:api",
        "Laravel\Passport\Http\Middleware\CheckClientCredentials",
    ];

    public function __construct(BaseRouter $router)
    {
        $this->baseRouter = $router;
        $this->routes = $router->routes;
        $this->container = $router->container;
        $this->events = $router->events;
        $this->middleware = $router->middleware;
        $this->middlewareGroups = $router->middlewareGroups;
        $this->middlewarePriority = $router->middlewarePriority;
        $this->binders = $router->binders;
        $this->patterns = $router->patterns;
        $this->groupStack = $router->groupStack;
    }

    public function dispatch(Request $request)
    {
        // snapshot original state
        $originalRoute = $this->baseRouter->current;
        $originalRequest = $this->baseRouter->currentRequest;
        // dispatch internal request
        $this->currentRequest = $request;
        $this->current = $this->routes->match($request);
        $this->container->instance('request', $request);

        $response = $this->dispatchToRoute($request);

        // recover to original state
        $this->baseRouter->current = $originalRoute;
        $this->baseRouter->currentRequest = $originalRequest;
        $this->container->instance('request', $originalRequest);
        $this->container->instance(Route::class, $originalRoute);

        return $response;
    }

    public function gatherRouteMiddleware(Route $route)
    {
        $middlewares = parent::gatherRouteMiddleware($route);
        $middlewares = array_filter($middlewares, [$this, 'shouldRunThisMiddleware']);

        return array_values($middlewares);
    }

    protected function shouldRunThisMiddleware($middleware)
    {
        foreach ($this->skipMiddlewares as $skipMiddleware) {
            if (is_string($middleware) && Str::is($skipMiddleware, $middleware)) {
                return false;
            }
        }

        return true;
    }
}
