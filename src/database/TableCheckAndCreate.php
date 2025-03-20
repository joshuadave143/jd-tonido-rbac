<?php

namespace jdTonido\RBAC\database;

use jdTonido\RBAC\database\Traits\RbacSqlTables;
use jdTonido\RBAC\Enums\TableNames;
use jdTonido\RBAC\Exceptions\InvalidRBACTables;

class TableCheckAndCreate extends PermissionTableSeeder{

    use RbacSqlTables;

    // Use enum values instead of an array
    private array $tableNames = [
        TableNames::Modules,
        TableNames::Permissions,
        TableNames::Reports,
        TableNames::Roles,
        TableNames::UserHasRoles,
        TableNames::ModuleHasPermissions,
        TableNames::ReportHasPermissions,
    ];

    // DROP TABLE rbac_user_has_roles;
    // DROP TABLE rbac_module_has_permissions;
    // DROP TABLE rbac_report_has_permissions;
    // DROP TABLE rbac_modules;
    // DROP TABLE rbac_permissions;
    // DROP TABLE rbac_reports;
    // DROP TABLE rbacs_roles;

    public function __construct(public $pgsqlInstance){

    }

    public function getRbacTables(){
        return $this->pgsqlInstance->select('tablename')
            ->from('pg_catalog.pg_tables')
            ->where("schemaname = 'public' 
            AND tablename LIKE 'rbac%'")
            ->readData();
    }

    public function checkTableExistence($rbacTables){
        // Loop through the result set
        while($result = pg_fetch_assoc($rbacTables)){
           // Check if the tablename is in the tableNames enum values
           $tableEnum = TableNames::tryFrom($result['tablename']);

            if ($tableEnum) {
                // If the table exists, remove it from the $tableNames array
                $this->tableNames = array_filter($this->tableNames, fn($table) => $table !== $tableEnum);
            }
        }
        
        // If there are any missing tables, throw an exception
        if( count($this->tableNames) > 0 ) {
            throw InvalidRBACTables::missingTables($this->tableNames);
        };
    }
    /**
     * Creates all the necessary RBAC-related tables in the PostgreSQL database.
     *
     * This method iterates through an array of table names (`$this->tableNames`) and 
     * dynamically calls the corresponding methods (like `roles()`, `permissions()`, etc.) 
     * to retrieve the table creation definitions. It then passes these definitions to 
     * the `createDBTable()` method of the PostgreSQL instance (`$this->pgsqlInstance`).
     * 
     * The table names are assumed to be methods in this class, each method returning 
     * the structure or schema for the corresponding table.
     */
    public function createTables(){
        // Loop through each table name defined in the $tableNames array
        foreach($this->tableNames as $tableName){
            // Dynamically call the method corresponding to the table name and create the table
            $this->pgsqlInstance->createDBTable(
                $this->{$tableName->value}() // Dynamically call the method using the table name
            );
        }

    }

    /**
     * Check if any RBAC tables are missing.
     *
     * @param array $rbacTables The list of retrieved RBAC tables.
     * @return bool Returns true if tables are missing; otherwise, false.
     */
    private function areTablesMissing($rbacTables)
    {
        return empty($rbacTables) || $this->pgsqlInstance->num_rows === 0;
    }

    public function run(){
        // Retrieve existing RBAC tables
        $rbacTables = $this->getRbacTables();

        // Check if tables exist, and create them if they don't
        if ($this->areTablesMissing($rbacTables)) {
            $this->createTables();

            $rbacTables = $this->getRbacTables(); // Refresh the table list after creation
        }

        // Validate the existence of required tables
        $this->checkTableExistence($rbacTables);

        // Populate the permissions table
        $this->seedPermissions($this->pgsqlInstance);

    }

}