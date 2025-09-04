<?php

namespace App\Routers;

use App\Traits\ResponseTrait;
use App\Middlewares\CheckAccessMiddleware;

class Router {
    use ResponseTrait;
    private $routes = [];
    private $postData;
    private $access;

    public function __construct()
    {
        $this->postData = getPostDataInput();
        $this->access = new CheckAccessMiddleware();
    }

    public function get($version, $path, $controller, $method, $access=false) {
        $path = '/' . $version . $path;
        $this->routes[$version]['GET'][$path] = ['controller' => $controller, 'method' => $method, 'request' => '', "requestMethod" => "get", "access" => $access];
    }

    public function post($version, $path, $controller, $method, $access=false) {
        $path = '/' . $version . $path;
        $this->routes[$version]['POST'][$path] = ['controller' => $controller, 'method' => $method, 'request' => $this->postData, "requestMethod" => "post", "access" => $access];
    }

    public function put($version, $path, $controller, $method, $access=false) {
        $path = '/' . $version . $path;
        $this->routes[$version]['PUT'][$path] = ['controller' => $controller, 'method' => $method, 'request' => $this->postData, "requestMethod" => "put", "access" => $access];
    }

    public function delete($version, $path, $controller, $method, $access=false) {
        $path = '/' . $version . $path;
        $this->routes[$version]['DELETE'][$path] = ['controller' => $controller, 'method' => $method, 'request' => '', "requestMethod" => "delete", "access" => $access];
    }

    public function resolve($version, $requestMethod, $path) {
        $path = '/' . $version . '/' . $path;
        $matchedRoute = null;

        // Match routes with variable patterns
        foreach ($this->routes[$version][$requestMethod] as $routePath => $route) {
            if ($this->isVariablePattern($routePath)) {
                $pattern = $this->getPatternFromRoute($routePath);
                if (preg_match($pattern, $path, $matches)) {
                    $matchedRoute = $route;
                    break;
                }
            } elseif ($routePath === $path) {
                $matchedRoute = $route;
                break;
            }
        }

        if ($matchedRoute) {
            $controller = $matchedRoute['controller'];
            $method = $matchedRoute['method'];
            $requestMethod = $matchedRoute['requestMethod'];
            $request = $matchedRoute['request'];
            $access = $matchedRoute['access'];
            if($access) $this->access->checkAccess($access);

            $controllerInstance = new $controller();
            if (isset($matches) && count($matches) && $requestMethod != "put") {
                $controllerInstance->$method($matches["id"]);
            } else {
                if($requestMethod == 'post') $controllerInstance->$method($request);
                else if($requestMethod == 'put' && isset($matches)) $controllerInstance->$method($matches["id"], $request);
                else $controllerInstance->$method();
            }
            exit();
        } else {
            return $this->sendResponse(null, "Not Found", true, HTTP_NotFOUND);
        }
    }

    private function isVariablePattern($path) {
        return strpos($path, '{') !== false && strpos($path, '}') !== false;
    }

    private function getPatternFromRoute($routePath) {
        $pattern = preg_replace('/\{([^\/]+)\}/', '(?<$1>[^\/]+)', $routePath);
        return '#^' . $pattern . '$#';
    }
}
