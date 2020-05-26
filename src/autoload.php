<?php

// define BASE_DIR global constant

define('BASE_DIR', dirname(__FILE__) . '/');

// set up flagrantly non-PSR-4-compliant autoloader

spl_autoload_register(function ($class) {
    $file = BASE_DIR . str_replace('\\', '/', $class) . '.php';
    require $file;
});
