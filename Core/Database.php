<?php

namespace Core;

use PDO;
use PDOStatement;

/**
 * Wraps around PDO to provide a more convenient way to interact with the database
 */
class Database {
    public PDO $con;

    public PDOStatement $statement;

    /**
     * @param array $config
     * @param string $username
     * @param string $password
     */
    public function __construct(array $config, string $username, string $password) {
        $dsn = "{$config['pdo_driver']}:" . http_build_query(array_diff_key($config, ['pdo_driver' => 1]), '', ';');

        $this->con = new PDO($dsn, $username, $password, [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }

    public function query($query, $params = []): PDOStatement {
        $final = interpolateQuery($query, $params);
        file_put_contents("php://stdout", "Query: $final\n");

        $this->statement = $this->con->prepare($query);
        $this->statement->execute($params);
        return $this->statement;
    }
}