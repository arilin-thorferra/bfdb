<?php

/**
 * Configuration settings
 */
return [
    // database setup
    'db_adapter' => 'Sqlite',
    'dsn'        => 'sqlite:' . BASE_DIR . 'db/bfdb.db',
    // arbitrary session ID
    'session'    => '12a6ee8416a1ef91786416de380c89aa0f2c2a84aea5fcc5',
    // debug mode
    'debug'      => true,
];
