<?php

declare(strict_types=1);

namespace PHPCore\Core\Infrastructure;

use PHPCore\Core\System\Config;
use RuntimeException;
use PDOException;
use Exception;
use PDO;

/**
 * Provides a static database utility that manages a PDO connection and offers simplified methods for executing
 * parameterized queries and retrieving results.
 *
 * @author Julius Derigs <julius@derigs.top
 */
class Database
{
    private static ?PDO $instance = null;
    private static string $dbType;
    private static string $host;
    private static string $user;
    private static string $pass;
    private static string $port;
    private static string $db;

    ####################################################################################################################
    # --- PUBLIC METHODS --------------------------------------------------------------------------------------------- #
    ####################################################################################################################

    /**
     * Returns a single PDO instance (singleton).
     *
     * @return PDO
     */
    public static function connect(): PDO
    {
        $config = Config::get('database');

        self::$dbType = $_ENV['DB_TYPE'] ?? $config['type'];
        self::$host = $_ENV['DB_HOST'] ?? $config['host'];
        self::$user = $_ENV['DB_USER'] ?? $config['user'];
        self::$pass = $_ENV['DB_PASS'] ?? $config['pass'];
        self::$port = $_ENV['DB_PORT'] ?? $config['port'];
        self::$db = $_ENV['DB_NAME'] ?? $config['name'];

        try {
            $dsn = match (self::$dbType) {
                'mysql' => 'mysql:host=' . self::$host
                    . ';port=' . self::$port
                    . ';dbname=' . self::$db,

                'pgsql' => 'pgsql:host=' . self::$host
                    . ';port=' . self::$port
                    . ';dbname=' . self::$db,

                default => throw new RuntimeException(
                    'Unsupported database type: ' . self::$dbType
                ),
            };

            $con = new PDO(
                $dsn,
                self::$user,
                self::$pass
            );

            $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $con->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            self::$instance = $con;
        } catch (PDOException $exception) {
            throw new RuntimeException(
                'Database connection failed: ' . $exception->getMessage()
            );
        }

        return self::$instance;
    }

    /**
     * Execute a SELECT query and return all results.
     *
     * @param string $query
     * @param array $params
     * @return array
     */
    public static function select(string $query, array $params = []): array
    {
        try {
            $stmt = self::connect()->prepare($query);
            $stmt->execute($params);

            return $stmt->fetchAll();
        } catch (Exception $exception) {
            throw new RuntimeException('[Database::select()] ' . $exception->getMessage());
        }
    }

    /**
     * Execute INSERT/UPDATE/DELETE query.
     *
     * @param string $query
     * @param array $params
     * @return bool
     */
    public static function execute(string $query, array $params = []): bool
    {
        try {
            return self::connect()->prepare($query)->execute($params);
        } catch (Exception $exception) {
            throw new RuntimeException('[Database::execute()] ' . $exception->getMessage());
        }
    }
}
