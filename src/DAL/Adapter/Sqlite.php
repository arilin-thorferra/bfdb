<?php

declare(strict_types=1);

namespace DAL\Adapter;

use PDO;
use Settings;

class Sqlite
{
    private $pdo;

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

    public function getPdo() : PDO
    {
        return $this->pdo;
    }
    
    public function lastInsertId()
    {
        $stmt = $this->pdo->query('select last_insert_rowid();');
        return $stmt->fetchColumn();
    }
}
