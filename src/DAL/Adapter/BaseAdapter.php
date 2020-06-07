<?php

declare(strict_types=1);

namespace DAL\Adapter;

use PDO;

abstract class BaseAdapter
{
    /**
     * PDO storage
     */
    protected $pdo;

    /**
     * Child class must define a constructor. The constructor is required to
     * set $this->pdo and perform any other database connection init steps.
     */
    abstract public function __construct();

    /**
     * Return the PDO
     *
     * @return PDO
     */
    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    /**
     * Return the last inserted ID
     */
    abstract public function lastInsertId();
}
