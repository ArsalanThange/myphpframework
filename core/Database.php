<?php

namespace Core;

class Database
{
    /**
     * The active PDO instance static.
     *
     * @var PDO
     */
    private static $instance = null;

    /**
     * The active PDO connection.
     *
     * @var PDO
     */
    private $connection;

    /**
     * Host name for database.
     *
     * @var string
     */
    private $db_host;

    /**
     * Username for database.
     *
     * @var string
     */
    private $db_username;

    /**
     * Password for database.
     *
     * @var string
     */
    private $db_password;

    /**
     * Name of the database to connect with.
     *
     * @var string
     */
    private $db_name;

    /**
     * Return instance of currently connected database.
     *
     * @return \PDO
     */

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Create a new database connection instance.
     *
     * @return \PDO
     */
    public function __construct()
    {
        $this->db_host = getConfig('db_host');
        $this->db_username = getConfig('db_username');
        $this->db_password = getConfig('db_password');
        $this->db_name = getConfig('db_name');

        try {
            $conn_string = "mysql:host=" . $this->db_host . ";dbname=" . $this->db_name;
            $pdo = new \PDO($conn_string, $this->db_username, $this->db_password);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->connection = $pdo;

            return $this->connection;
        } catch (\PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    private function __clone()
    {}

    /**
     * Prepare and Execute Database Queries.
     *
     * @param string $query SQL Query which is to be executed in PDO
     * @param array $binds  Bind values which are to be bound during execution
     * @return \PDO
     * 
     * @throws \Exception
     */
    public function query($query, $binds)
    {
        $sql = $this->connection->prepare($query);

        if (count($binds)) {
            foreach ($binds as $key => &$bind) {
                $sql->bindParam($key + 1, $bind);
            }
        }

        if ($sql->execute()) {
            return $sql;
        } else {
            throw new Exception("Something went wrong");
        }
    }
}
