<?php
namespace jdTonido\RBAC\Exceptions;

use InvalidArgumentException;

class InvalidPGSQLInstance extends InvalidArgumentException{
    public static function invalidInstance($instance){
        return new static("Invalid instance provided. Expected instance of pgsqlDriver, got: " . get_class($instance));
    }
}