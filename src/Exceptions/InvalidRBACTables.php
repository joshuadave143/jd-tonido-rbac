<?php
namespace jdTonido\RBAC\Exceptions;

use InvalidArgumentException;

class InvalidRBACTables extends InvalidArgumentException{
    public static function missingTables(array  $missingTables){
        // Convert the enum cases to their string values before joining
        $missingTableNames = array_map(fn($table) => $table->value, $missingTables);
        
        return new static("The following RBAC table(s) are missing: " . implode(", ", $missingTableNames));
    }
}