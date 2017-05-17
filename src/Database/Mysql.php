<?php

namespace App\Database;

class Mysql implements Database
{
    /**
     * @var \PDO
     */
    private $connection;

    /**
     * @inheritdoc
     */
    public function __construct(string $host, string $dbName, string $username, string $password)
    {
        try {
            $this->connection = new \PDO(sprintf('mysql:host=%s', $host, $dbName), $username, $password);

            $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->connection->exec(sprintf('CREATE DATABASE IF NOT EXISTS %s', $dbName));
            $this->connection->exec(sprintf('USE %s', $dbName));
        } catch (\PDOException $e) {
            throw new \Exception(sprintf('Error connecting to database, reason: %s', $e->getMessage()));
        }
    }

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->connection->exec(
            'CREATE TABLE IF NOT EXISTS metrics (
                  id INT NOT NULL AUTO_INCREMENT,
                  unit_id INT NOT NULL,
                  metric VARCHAR(255),
                  minimum INT NOT NULL,
                  maximum INT NOT NULL,
                  mean INT NOT NULL,
                  median INT NOT NULL,
                  sample_size INT NOT NULL,
                  `date` TIMESTAMP NOT NULL,
                  PRIMARY KEY (id)
              );'
        );
    }

    /**
     * @return \PDO
     */
    public function getConnection() {
        return $this->connection;
    }
}
