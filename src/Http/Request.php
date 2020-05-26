<?php

declare(strict_types=1);

namespace Http;

use InvalidArgumentException;

/**
 * Request wrapper class
 */
class Request
{
    protected $method = '';
    protected $url = [];

    /**
     * Constructor
     */
    public function __construct(
        $url='', $method=''
    ) {
        $this->url = parse_url($url);
        if ($this->url === false) {
            throw new InvalidArgumentException(
                "Unparseable URL '$url' passed to \Http\Request"
            );
        }
        $this->method = strtolower($method);
    }

    /**
     * Return one or all URL components
     *
     * @param string $key
     * @return mixed
     */
    public function getUrl(string $key='')
    {
        if ($key) {
            return $this->url[$key];
        }
        return $this->url;
    }

    /**
     * Get one or all query string values
     * 
     * With no parameter, a key/value array is returned; with a single
     * parameter (the key), that value is returned.
     *
     * @param string $key
     * @return mixed
     */
    public function getQuery(string $key='')
    {
        parse_str($this->url['query'], $query);
        if ($key) {
            return $query[$key];
        }
        return $query;
    }

    /**
     * Return the path component of the request
     *
     * @return string
     */
    public function getPath() : string
    {
        return urldecode($this->url['path']);
    }

    /**
     * Return the HTTP method of the request
     *
     * @return string
     */
    public function getMethod() : string
    {
        return $this->method;
    }
}
