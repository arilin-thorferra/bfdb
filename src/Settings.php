<?php

declare(strict_types=1);

/**
 * Settings singleton class
 * 
 * While this is a singleton, the values are immutable. The first time
 * this class is accessed, settings are loaded from <config/settings.php>.
 */
class Settings
{
    /**
     * Settings variable storage
     *
     * @var array|null
     */
    private static $settings = null;

    /**
     * Routes variable storage
     *
     * @var array|null
     */
    private static $routes = null;

    /**
     * Return a settings value
     *
     * @param string $key
     * @return mixed
     */
    public static function get(string $key)
    {
        if (is_null(self::$settings)) {
            self::$settings = require BASE_DIR . 'config/settings.php';
        }
        if (array_key_exists($key, self::$settings)) {
            return self::$settings[$key];
        }
        throw new OutOfBoundsException("Settings key '$key' not set");
    }

    /**
     * Return the route array
     *
     * @return array
     */
    public static function routes(): array
    {
        if (is_null(self::$routes)) {
            self::$routes = require BASE_DIR . 'config/routes.php';
        }
        return self::$routes;
    }
}
