<?php

declare(strict_types=1);

namespace Bfdb;

use Bfdb\Http\Request;
use Bfdb\Route\Route;
use Bfdb\Route\Handler;
use Bfdb\Context;
use Bfdb\Settings;

/**
 * Application Front Controller
 */
class App
{
    /**
     * The run() function does all the work: initializing the request object,
     * resolving the route, calling the action handler, and sending the
     * response, appending optional debugging information.
     *
     * @return void
     */
    public function run(): void
    {
        // initialize session
        session_name(Settings::get('session'));
        session_start();
        if (empty($_SESSION['token'])) {
            $_SESSION['token'] = bin2hex(random_bytes(32));
        }
        // instantiate request
        $request = new Request(
            $_SERVER['REQUEST_URI'],
            $_SERVER['REQUEST_METHOD']
        );
        // load and process routes
        $routes = Settings::routes();
        $handler = new Handler($routes);
        $route = $handler->findRoute($request);
        // execute request
        $response = $handler->execute($request, $route);
        if (Settings::get('debug')) {
            $response->addToBody($this->debugInfo($request, $route));
        }
        // send response
        if (headers_sent()) {
            throw new \RuntimeException('Headers already sent');
        }
        http_response_code($response->getStatus());
        $headers = $response->getHeaders();
        foreach ($headers as $header => $value) {
            header("$header: $value");
        }
        echo $response->getBody();
    }

    /**
     * Add debugging information to the end of the response.
     *
     * @param Request $request
     * @param Route $route
     * @return string
     */
    private function debugInfo(Request $request, Route $route): string
    {
        $info =
            "<p class='debuginfo'>" .
            'Request path: ' .
            $request->getPath() .
            '<br>Request method: ' .
            $request->getMethod();
        if ($route->isSet()) {
            $info .=
                '<br>Resolved Route: ' .
                $route->getClass() .
                '::' .
                $route->getMethod() .
                '<br>Route arguments: ' .
                var_export($route->getArgs(), true);
        }
        if (Context::has('debugmsg')) {
            $info .= '<br><br>' . Context::get('debugmsg');
        }
        $info .= '</p>';
        return $info;
    }
}
