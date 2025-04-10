<?php

namespace jdTonido\RBAC\Helpers;

use jdTonido\RBAC\core\Views\UnauthorizedPage;
use jdTonido\RBAC\Enums\TableNames;
use jdTonido\RBAC\core\Traits\Views;

class NestedDynamicMethodWrapperForPermission {
    use Views;
    private array $groupedPermissions = [];

    public function __construct(private array $data) {
        // Group permissions by module_name, transforming keys into PascalCase

        foreach ($this->data as $permission) {
            $module = $this->normalizeModuleName($permission["mr_name"]);
            $this->groupedPermissions[$module][] = $permission["name"];
        }
    }

    public function __call($name, $arguments) {
        $moduleName = ucfirst($name);
       
        if (isset($this->groupedPermissions[$moduleName])) {
 
            return new class($this->groupedPermissions[$moduleName]) {
                private array $permissions;

                public function __construct(array $permissions) {
                    $this->permissions = $permissions;
                }

                public function __call($name, $arguments) {
                    $permission = ucfirst($name);
                    return in_array($permission, $this->permissions);
                }
            };
        }
        
        return new class($this) {
           
            public function __construct( private NestedDynamicMethodWrapperForPermission $parent) {
            }
            public function __call($name, $arguments) {
                // dd($arguments[0] == '403');
                if(@$arguments[0] == '403') $this->parent->UnauthorizedPage();
                return false;
            }
        };
        // return "Invalid module: '$moduleName'.";
    }

    private function normalizeModuleName(string $name): string {
        // Remove special characters and convert to PascalCase
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $name)));
    }
}
