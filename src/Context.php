<?php

declare(strict_types=1);

/**
 * Context storage singleton class
 * 
 * Stores key/value pairs across the current context. This is essentially
 * an implementation of the Registry Pattern.
 */
class Context
{
    /**
     * Context variable storage
     *
     * @var array
     */
    private static $context = [];
    private static $dba;

    /**
     * Get a value from context storage
     *
     * @param string $key
     * @return mixed
     */
    public static function get(string $key)
    {
        if (self::has($key)) {
            return self::$context[$key];
        }
        throw new OutOfBoundsException("Context key '$key' not set");
    }

    /**
     * Set a variable in context storage
     *
     * @param string $key
     * @param [type] $val
     * @return void
     */
    public static function set(string $key, $val): void
    {
        self::$context[$key] = $val;
    }

    /**
     * Check for the existence of a key in context storage
     *
     * @param string $key
     * @return boolean
     */
    public static function has(string $key): bool
    {
        return array_key_exists($key, self::$context);
    }

    public static function add(string $key, string $val, string $delimiter = "\n"): void
    {
        if (self::has($key)) {
            self::$context[$key] .= $delimiter . $val;
        } else {
            self::$context[$key] = $val;
        }
    }

    public static function debug($message)
    {
        if (\Settings::get('debug')) {
            self::add('debugmsg', htmlspecialchars($message));
        }
    }

    public static function getDba()
    {
        if (!self::$dba) {
            $db_adapter = '\\DAL\\Adapter\\' . Settings::get('db_adapter');
            self::$dba = new $db_adapter();
        }
        return self::$dba;
    }
}
