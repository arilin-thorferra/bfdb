<?php

function handler($e)
{
    $response = new \Http\Response(500);
    http_response_code(500);
    if (Settings::get('debug')) {
        $response->addToBody(
        '<b style="color:#C00">' . $e->getMessage() . '</b>' .
        '<pre style="font:14px/1.5 Monaco">' . $e->getTraceAsString() . '</pre>'
        );
    }
    echo $response->getBody();
}

set_exception_handler('handler');
