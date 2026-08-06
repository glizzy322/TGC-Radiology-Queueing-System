<?php

namespace App\Core;

class Router
{
    protected array $routes = [];

    public function get($uri, $controllerAction)
    {
        $this->routes['GET'][$uri] = $controllerAction;
    }

    public function post($uri, $controllerAction)
    {
        $this->routes['POST'][$uri] = $controllerAction;
    }

    public function dispatch($method, $uri)
    {
        if (isset($this->routes[$method][$uri])) {
            $controllerAction = $this->routes[$method][$uri];
            [$controller, $action] = $controllerAction;
            $instance = new $controller();
            return $instance->$action();
        }

        http_response_code(404);
        echo "404 Not Found";
    }
}
