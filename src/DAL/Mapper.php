<?php

declare(strict_types=1);

namespace DAL;

use Context;
use RuntimeException;

/**
 * Base class for data mapping. This will obtain the database adapter
 * singleton from Context, creating it if necessary, and execute a PDO
 * query. Functions build on one another from low level to (medium) high.
 * This class should be extended by mappers that correspond to tables;
 * a mapper only technically needs to define a TABLE constant with the name
 * of the table it maps to, but it can (and usually should) define its own
 * higher-level functions, such as a User::create() function that handles
 * password hashing.
 * 
 * While mappers can do validation, form validation is meant to be handled
 * by the Form class.
 */
abstract class Mapper
{
    /**
     * Table name of a child class
     */
    const TABLE = false;

    /**
     * Prepare a PDO statement based on the provided query.
     *
     * @param string $query
     * @return \PDOStatement
     */
    protected function do(string $query): \PDOStatement
    {
        $dba = Context::getDba();
        $pdo = $dba->getPdo();
        return $pdo->prepare($query);
    }

    /**
     * Execute a query. Returns TRUE if query succeeds, FALSE on failure.
     *
     * @param string $query
     * @param array $params
     * @return bool
     */
    public function exec(string $query, $params = []): bool
    {
        $stmt = $this->do($query);
        return $stmt->execute((array) $params);
    }

    /**
     * Execute a query and return a result set
     *
     * @param string $query
     * @param mixed $params
     * @return PDOStatement
     */
    public function query(string $query, $params = []): \PDOStatement
    {
        $stmt = $this->do($query);
        $stmt->execute((array) $params);
        return $stmt;
    }

    /**
     * Read a single record. Returns an associative array, or FALSE
     * on failure.
     *
     * @param string $query
     * @param array $params
     * @return mixed
     */
    public function read(string $query, $params = [])
    {
        $stmt = $this->query($query, $params);
        return $stmt->fetch();
    }

    /**
     * Read a set of records. Returns an array of associative arrays, one for
     * each returned column, using PDO::fetchAll().
     *
     * @param string $query
     * @param array $params
     * @return array
     */
    public function readSet(string $query, $params = []): array
    {
        $stmt = $this->query($query, $params);
        return $stmt->fetchAll();
    }

    /**
     * Return the last inserted ID. This value is usually an integer, but
     * this is column/database dependent.
     *
     * @return mixed
     */
    public function lastInsertId()
    {
        $dba = Context::getDba();
        return $dba->lastInsertId();
    }

    /**
     * Checks to make sure the TABLE constant is defined in a child class.
     *
     * @param string $func
     * @return void
     */
    private function checkTable(string $func)
    {
        if (!$this::TABLE) {
            $class = get_class($this);
            throw new RuntimeException("Mapper::$func called, but '$class' does not define TABLE constant");
        }
    }

    /**
     * Insert a new row into a mapped table. Returns the ID of the newly
     * inserted row or FALSE on error.
     *
     * @param array $data
     * @return mixed
     */
    public function insert(array $data)
    {
        $this->checkTable('insert');
        $cols = [];
        $vals = [];
        $params = [];
        foreach ($data as $col => $param) {
            $cols[] = $col;
            $params[] = $param;
            $vals[] = '?';
        }
        $query = 'INSERT INTO ' . $this::TABLE . ' (' . implode(', ', $cols) .
            ') VALUES (' . implode(', ', $vals) . ')';
        $res = $this->exec($query, $params);
        return $res ? $this->lastInsertId() : false;
    }

    /**
     * Update an existing row in a mapped table. Returns TRUE on success,
     * FALSE on failure.
     *
     * @param [type] $row
     * @param array $data
     * @param string $key
     * @return boolean
     */
    public function update($row, array $data, string $key = 'id'): bool
    {
        $this->checkTable('update');
        $cols = [];
        $vals = [];
        $params = [];
        foreach ($data as $col => $param) {
            $cols[] = $col;
            $params[] = $param;
            $vals[] = "$col = ?";
        }
        $params[] = $row;
        $query = 'UPDATE ' . $this::TABLE . ' SET ' . implode(', ', $vals) .
            " WHERE $key = ?";
        return $this->exec($query, $params);
    }

    /**
     * Return a complete row in a mapped table, specified by ID or by
     * another optional key field (e.g., email).
     * 
     *     // get user record for id = $user_id
     *     $user = $userMapper->find($user_id);
     * 
     *     // get user record for user matching email $email
     *     $user = $userMapper->find($email, 'email');
     *
     * @param mixed $param
     * @param string $key
     * @return mixed
     */
    public function find($param, string $key = 'id')
    {
        $this->checkTable('find');
        $query = 'SELECT * FROM ' . $this::TABLE . " WHERE $key = ?";
        return $this->read($query, $param);
    }

    /**
     * Return an array of rows from a mapped table, specified by a provided
     * WHERE clause or just a column name, e.g.:
     * 
     *     // find all posts by user $user
     *     $posts = $postMapper->findSet('user_id', $user);
     * 
     *     // find all users with total_post_count < 10
     *     $newbies = $userMapper->findSet('total_post_count < ?', 10);
     *
     * @param string $where
     * @param mixed $param
     * @return array
     */
    public function findSet(string $where, $param): array
    {
        $this->checkTable('findSet');
        if (strpos($where, ' ') === false) {
            $where .= ' = ?';
        }
        $query = 'SELECT * FROM ' . $this::TABLE . " WHERE $where";
        return $this->readSet($query, $param);
    }

    /**
     * Return a count of rows that match a WHERE clause or provided column
     * name, similar to find() above. You can specify a column name to count
     * as an optional third parameter. Specifying *no* parameters will return
     * a count of all the records in the table (e.g., no WHERE clause).
     *
     * @param string $where
     * @param string $param
     * @param string $col
     * @return int
     */
    public function count(string $where = '', $param = '', string $col = '*')
    {
        $this->checkTable('count');
        $query = "SELECT COUNT($col) FROM " . $this::TABLE;
        if ($where) {
            if (!$param) {
                throw new RuntimeException('Mapper::count called with WHERE and no PARAM value');
            }
            if (strpos($where, ' ') === false) {
                $where .= ' = ?';
            }
            $query .= " WHERE $where";
        }
        $res = $this->query($query, $param);
        return $res->fetchColumn();
    }
}
