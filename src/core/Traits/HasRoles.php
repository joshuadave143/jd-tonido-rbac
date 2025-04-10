<?php
namespace jdTonido\RBAC\core\Traits;

use jdTonido\RBAC\core\RoleService;
use jdTonido\RBAC\Exceptions\InvalidPGSQLInstance;
use jdTonido\RBAC\Factories\PgsqlDriverFactory;
use jdTonido\RBAC\Helpers\PgsqlResultConverter;

trait HasRoles
{
    public ?\driver\pgsqlDriver $pgsqlInstance = null;
    public RoleService $roleService;

    // public function initHasRoles(){
    //     // Initialize RoleService with the pgsqlDriver instance
    //     $this->roleService = new RoleService($this->pgsqlInstance);
    // }


    public function fetchRoleByAccountID($accountID){
        return PgsqlResultConverter::array($this->roleService->getRoleByAccountID($accountID));
    }

    public function isUserHasRole($accountID){
        return $this->roleService->getRoleByAccountID($accountID)?true:false;
    }

    public function getRoles(){
        return $this->roles;
    }

    public function setRoles($roles){
        $this->roles = $roles;
    }
}