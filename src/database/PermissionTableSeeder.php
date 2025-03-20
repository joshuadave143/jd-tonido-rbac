<?php
namespace jdTonido\RBAC\database;

use jdTonido\RBAC\Enums\Permissions;
use jdTonido\RBAC\Enums\TableNames;

class PermissionTableSeeder{

    public function getPermissions($pgsqlInstance){
        return $pgsqlInstance->select()
            ->from(TableNames::Permissions->value)
            ->readData();
    }

    public function seedPermissions($pgsqlInstance){

        $this->getPermissions($pgsqlInstance);

        if( $pgsqlInstance->num_rows > 0 ) return;


        $permissions = [
            [
                'name' => Permissions::Add->value,
                'type' => 'module',
            ],
            [
                'name' => Permissions::View->value,
                'type' => 'module',
            ],
            [
                'name' => Permissions::Edit->value,
                'type' => 'module',
            ],
            [
                'name' => Permissions::Delete->value,
                'type' => 'module',
            ],
            [
                'name' => Permissions::Allow->value,
                'type' => 'report',
            ],
        ];
        $pgsqlInstance->table(TableNames::Permissions->value)
            ->insert_set_columnNames(['name','type'])
            ->insert_set_values( $permissions)
            ->insertData();
    }
}