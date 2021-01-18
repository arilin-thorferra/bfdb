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

    /**
     * Database adapter
     *
     * @var \DAL\Adapter\BaseAdapter
     */
    private static $dba;

    /**
     * Get a value from context storage
     *
     * @param string $key
     * @return mixed
     * @throws OutOfBoundsException if key not set
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
     * @param mixed $val
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

    /**
     * Append a string value to a key in context storage
     *
     * @param string $key
     * @param string $val
     * @param string $delimiter
     * @return void
     */
    public static function add(
        string $key,
        string $val,
        string $delimiter = "\n"
    ): void {
        if (self::has($key)) {
            self::$context[$key] .= $delimiter . $val;
        } else {
            self::$context[$key] = $val;
        }
    }

    /**
     * Add a debugging message to context storage
     *
     * If debug mode is set, the string will be added and displayed as part
     * of the request output
     *
     * @param string $message
     * @return void
     */
    public static function debug(string $message): void
    {
        if (\Settings::get('debug')) {
            self::add('debugmsg', htmlspecialchars($message));
        }
    }

    /**
     * Add a message to the flash array
     *
     * @param string $message
     * @param string $class
     * @return void
     */
    public static function flash(string $message, string $class = 'flash'): void
    {
        $_SESSION['flash'][] = [$class, $message];
    }

    /**
     * Return the flash array as HTML <div> tags
     *
     * @return string
     */
    public static function getFlash()
    {
        $retval = '';
        if (isset($_SESSION['flash'])) {
            foreach ($_SESSION['flash'] as $message) {
                $retval .= "<div class='{$message[0]}'>{$message[1]}</div>";
            }
        }
        unset($_SESSION['flash']);
        return $retval;
    }

    /**
     * Get the data access layer adapter, instantiating it if necessary
     *
     * @return \DAL\Adapter\BaseAdapter
     */
    public static function getDba()
    {
        if (!self::$dba) {
            $db_adapter = '\\DAL\\Adapter\\' . Settings::get('db_adapter');
            self::$dba = new $db_adapter();
        }
        return self::$dba;
    }
}
