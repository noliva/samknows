<?php

namespace App\Database;

interface Database
{
    /**
     * @param string $host
     * @param string $dbName
     * @param string $username
     * @param string $password
     */
    public function __construct(string $host, string $dbName, string $username, string $password);

    /**
     * Creates the tables needed.
     */
    public function setUp();
}
