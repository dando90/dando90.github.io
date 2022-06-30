<?php

namespace Core;
use Exception;

class Router
{
    public $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'DELETE' => []
    ];

    public static function load($file)
    {
        $router = new static;
        require $file;
        return $router;
    }

    public function get($uri, $controller)
    {
        $this->routes['GET'][$uri] = $controller;
    }

    public function post($uri, $controller)
    {
        $this->routes['POST'][$uri] = $controller;
    }
    
    public function put($uri, $controller)
    {
        $this->routes['PUT'][$uri] = $controller;
    }

    public function delete($uri, $controller)
    {
        $this->routes['DELETE'][$uri] = $controller;
    }

    public function direct($uri, $requestType)
    {
        if(preg_match('/(api\/[a-z]+)\/([0-9]+)$/', $uri, $matches)){
            if(array_key_exists($matches[1], $this->routes[$requestType])){
                $_GET['id'] = $matches[2];
                return $this->callAction(
                    ...explode('@',$this->routes[$requestType][$matches[1]])
                );
            }
        }
        if(array_key_exists($uri, $this->routes[$requestType])){
            return $this->callAction(
                ...explode('@',$this->routes[$requestType][$uri])
            );
        }
        throw new Exception('No route defined for this URI.');
    }
    
    protected function callAction($controller, $action)
    {
        $controller = "App\\Controllers\\{$controller}";
        $controller = new $controller;
        if (!method_exists($controller,$action)) {
            throw new Exception(
                "{$controller} does not respond to the {$action} action."
            );
        }
        return $controller->$action();
    }
}