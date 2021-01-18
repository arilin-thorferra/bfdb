<?php

// ======================== //
// NINAF Is Not A Framework //
// ======================== //

// Set up BASE_DIR global so the application knows where it's installed

define('BASE_DIR', dirname(__FILE__) . '/');

// Register a class autoloader

spl_autoload_register(function ($class) {
    $file = BASE_DIR . str_replace('\\', '/', $class) . '.php';
    require $file;
});

// Register an exception handler

set_exception_handler(function ($e) {
    // generate an HTTP 500 (Interal Server Error) response
    $response = new \Http\Response(500);
    http_response_code(500);
    // if debug mode is enabled, add a stack trace
    if (Settings::get('debug')) {
        $response->addToBody(
            '<b style="color:#C00">' .
                $e->getMessage() .
                '</b>' .
                '<pre style="font:12px/1.5 Monaco">' .
                $e->getTraceAsString() .
                '</pre>'
        );
    }
    echo $response->getBody();
});

// Run the application!

$app = new App();
$app->run();
