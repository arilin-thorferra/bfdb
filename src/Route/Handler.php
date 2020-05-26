<?php

declare(strict_types=1);

namespace Route;

use Http\Request;
use Http\Response;
use ReflectionClass;
use ReflectionMethod;
use RuntimeException;

/**
 * Route handler class
 * 
 * Resolves route objects to action classes and executes the class, returning
 * HTTP response objects based on success or failure.
 */
class Handler
{

    private $routes = [];

    /**
     * Load routes
     * 
     * @param array $routes
     */
    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    /**
     * Route parser. Takes a path from the Request URI and attempts to match
     * it with a route. Returns a route object, which will not be set if no
     * route was round.
     *
     * @param Request $request
     * @return Route
     */
    public function findRoute(Request $request) : Route
    {
        $path = $request->getPath();
        $found = new Route();
        foreach ($this->routes as $route => $view) {
            $pos = strpos($route, '(');
            // first check if this is a static route
            if ($pos === false) {
                if ($route == $path) {
                    $found->set($view[0], $view[1]);
                    break;
                }
                continue;
            }
            // see if dynamic route has the same static part
            if (strncmp($route, $path, $pos) !== 0) {
                continue;
            }
            // they do, so try a regex match
            if (preg_match("@^{$route}$@", $path, $matches)) {
                $found->set($view[0], $view[1], $matches);
                break;
            }
        }
        return $found;
    }

    /**
     * Execute a route by instantiating its class and trying to invoke a
     * method with the name "httpmethod_action" (e.g., "get_index"). A
     * response will always be returned -- if the action invocation fails,
     * execute() will return a 404, 405, or 500 response as appropriate.
     * 
     * Actions must return either \Http\Response or \Route\Route objects.
     * The former will be returned as is; the latter will create an internal
     * redirect.
     *
     * @param Request $request
     * @param Route $route
     * @return Response
     */
    public function execute(Request $request, Route $route) : Response
    {
        if (!$route->isSet()) {
            $error = "No route for '{$request->getPath()}'";
            error_log($error);
            \Context::debug($error);
            return new Response(404);
        }
        $action_class = 'Action\\' . $route->getClass();
        $action = new $action_class($request);
        $method = $route->getMethod();
        $call = $request->getMethod() . '_' . $method;
        if (!method_exists($action, $call)) {
            // class exists, but not the method, so 405 or 500 error
            $error = "'$action_class::$call' not found";
            error_log($error);
            $allowed = $this->findAllowedMethods($method, $action_class);
            if ($allowed) {
                $response = new Response(405);
                $response->setHeader('Allowed', $allowed);
            } else {
                $response = new Response(500);
            }
            \Context::debug($error);
            return $response;
        }
        $response = $action->$call($route->getArgs());
        // if we've received a Route, this is an internal redirect
        if ($response instanceof Route) {
            $response = $this->execute($request, $response);
        }
        return $response;
    }

    /**
     * Gather a list of allowed HTTP methods for an action class, given the
     * class name and the method in the class. If the class method name is
     * "foobar" and the action class contains "put_foobar" and "post_foobar"
     * methods, the return value will be "PUT, POST". This is used to construct
     * HTTP 405 (Method Not Allowed) responses.
     * 
     * Yes, using "method" for both HTTP methods and class methods is confusing.
     *
     * @param string $method
     * @param string $class
     * @return string
     */
    private function findAllowedMethods(string $method, string $class) : string
    {
        $action = new ReflectionClass($class);
        $methods = $action->getMethods(ReflectionMethod::IS_PUBLIC);
        $allowed = [];
        foreach ($methods as $class_method) {
            $method_name = $class_method->getName();
            if (strpos($method_name, '_') === false) {
                continue;
            }
            [$http_method, $action_method] = explode('_', $method_name);
            if ($action_method == $method) {
                $allowed[] = strtoupper($http_method);
            }
        }
        return implode(', ', $allowed);
    }
}
