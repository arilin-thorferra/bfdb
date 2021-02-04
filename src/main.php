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

define('BASE_DIR', dirname(__FILE__) . '/');

// Reluctantly load Composer's PSR-4 autoloader so we can use third-party
// plugins if we are forced, possibly at gunpoint. If the app immediately
// dies, run "composer dump-autoload" at the TOP LEVEL directory to create
// the vendor/ directory at the same level as the src/ directory.

require_once BASE_DIR . '../vendor/autoload.php';

// Register a snazzy (?) exception handler

set_exception_handler(function ($e) {
    // generate an HTTP 500 (Internal Server Error) response
    $response = new Bfdb\Http\Response(500);
    http_response_code(500);
    // if debug mode is enabled, add a stack trace
    if (Bfdb\Settings::get('debug')) {
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

$app = new Bfdb\App();
$app->run();
