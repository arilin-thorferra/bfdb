<?php

/* ========================
 * NINAF Is Not A Framework
 * ========================
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

// Set up BASE_DIR global so the application knows where it's installed

define("BASE_DIR", dirname(__FILE__) . "/");

// Register a class autoloader

spl_autoload_register(function ($class) {
    $file = BASE_DIR . str_replace("\\", "/", $class) . ".php";
    require $file;
});

// Register an exception handler

set_exception_handler(function ($e) {
    // generate an HTTP 500 (Internal Server Error) response
    $response = new \Http\Response(500);
    http_response_code(500);
    // if debug mode is enabled, add a stack trace
    if (Settings::get("debug")) {
        $response->addToBody(
            '<b style="color:#C00">' .
                $e->getMessage() .
                "</b>" .
                '<pre style="font:12px/1.5 Monaco">' .
                $e->getTraceAsString() .
                "</pre>"
        );
    }
    echo $response->getBody();
});

// Run the application!

$app = new App();
$app->run();
