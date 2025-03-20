<?php

namespace jdTonido\RBAC\helpers;

/**
 * PgsqlResultConverter
 *
 * A utility class for converting PostgreSQL query results into arrays.
 */
class PgsqlResultConverter{

    /**
     * Converts a PostgreSQL query result resource into an associative array.
     *
     * @param resource $pgsqlResult The PostgreSQL result resource from a query (returned by pg_query).
     * @return array An array of associative arrays representing each row of the result set.
     * @throws \InvalidArgumentException If the provided argument is not a valid PostgreSQL query result.
     */
    public static function array($pgsqlResult): array{
        $tmp = [];
        if(!$pgsqlResult){
            return $tmp;
        }
        // Validate that the provided result is a valid PostgreSQL result resource.
        if (!($pgsqlResult instanceof \PgSql\Result)) {
            throw new \InvalidArgumentException("Invalid PostgreSQL query result provided.");
        }

        
        // Fetch each row as an associative array and append it to the result array.
        while($result = pg_fetch_assoc($pgsqlResult)){
            $tmp[] = $result;
        }
        return $tmp;
    }
}