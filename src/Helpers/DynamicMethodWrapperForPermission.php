<?php

namespace jdTonido\RBAC\helpers;

use jdTonido\RBAC\core\Views\UnauthorizedPage;
use jdTonido\RBAC\Enums\Permissions;
use jdTonido\RBAC\Enums\TableNames;

class DynamicMethodWrapperForPermission {
    

    public function __construct(private array $data, public $pgsqlInstance) {
    }

    public function __call($name, $arguments) {
        // Convert camelCase method call to snake_case array key
        $key = (preg_replace('/([a-z])([A-Z])/', '$1_$2', $name));
        $key = ucfirst($key);
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }

        $results = $this->pgsqlInstance->select()
            ->from(TableNames::Permissions->value)
            ->where("name = '$key'")
            ->readData();
        if(!$results ){
            return "Invalid permission: '$key'.";
        }

        return "User does not have the right to $key";
    }
}
