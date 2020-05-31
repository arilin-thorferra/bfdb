<?php

declare(strict_types=1);

namespace Route;

/**
 * Route wrapper class
 */
class Route
{
    private $class = '';
    private $method = '';
    private $args = [];

    /**
     * Constructor
     *
     * @param string $class
     * @param string $method
     * @param array $args
     */
    public function __construct(
        string $class = '',
        string $method = '',
        array $args = []
    ) {
        if ($class) {
            $this->set($class, $method, $args);
        }
    }

    /**
     * Set the route
     *
     * @param string $class
     * @param string $method
     * @param array $args
     * @return void
     */
    public function set(string $class, string $method, array $args = [])
    {
        $this->class = $class;
        $this->method = $method;
        $this->args = $args;
    }

    /**
     * Test whether the route is set
     *
     * @return boolean
     */
    public function isSet(): bool
    {
        return $this->class ? true : false;
    }

    /**
     * Get the route's class
     *
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * Get the route's method
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Get the route's arguments
     *
     * @return array
     */
    public function getArgs(): array
    {
        return $this->args;
    }
}
