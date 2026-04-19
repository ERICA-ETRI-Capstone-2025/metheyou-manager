<?php
namespace App\Core;

use PDO;
use PDOException;

class Database {
    private static $mariaDb = null;
    private static $pgDb = null;

    public static function getMariaDb(): PDO {
        if (self::$mariaDb === null) {
            try {
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
                self::$mariaDb = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);
            } catch (PDOException $e) {
                die("MariaDB Connection failed: " . $e->getMessage());
            }
        }
        return self::$mariaDb;
    }

    public static function getPgDb(): PDO {
        if (self::$pgDb === null) {
            try {
                self::$pgDb = new PDO(POSTGRES_DSN, POSTGRES_USER, POSTGRES_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);
            } catch (PDOException $e) {
                die("PostgreSQL Connection failed: " . $e->getMessage());
            }
        }
        return self::$pgDb;
    }
}
