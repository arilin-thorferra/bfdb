<?php

declare(strict_types=1);

namespace DAL\Adapter;

use PDO;
use Settings;

/**
 * Database Adapter for SQLite 3
 */
class Sqlite extends BaseAdapter
{
    /**
     * Constructor. Instantiates a PDO based on the DSN provided in settings,
     * sets the default fetch mode, and turns on SQLite's foreign keys.
     */
    public function __construct()
    {
        $pdo = new PDO(
            Settings::get('dsn'),
            null,
            null,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
        $this->pdo = $pdo;
        $this->pdo->exec('PRAGMA foreign_keys = on;');
    }

    /**
     * Return the last inserted ID. This is *always* an integer with SQLite;
     * if the table's primary key is not an Integer, this will return
     * SQLite's internal ROWID value. (This should probably be changed to be
     * smarter.)
     *
     * @return int
     */
    public function lastInsertId(): int
    {
        $stmt = $this->pdo->query('select last_insert_rowid();');
        return $stmt->fetchColumn();
    }
}
