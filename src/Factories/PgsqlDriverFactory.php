<?php

namespace jdTonido\RBAC\Factories;

class PgsqlDriverFactory
{
    public static function createDriver(): \driver\pgsqlDriver
    {
        // Here you can create and return a pgsqlDriver instance
        // For example, you could connect to a database or do other setup tasks
        return new \driver\pgsqlDriver();  // Adjust based on your actual pgsqlDriver construction
    }
}