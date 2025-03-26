<?php
namespace jdTonido\RBAC\core\Traits;

use jdTonido\RBAC\core\RoleService;
use jdTonido\RBAC\Exceptions\InvalidPGSQLInstance;
use jdTonido\RBAC\Factories\PgsqlDriverFactory;

trait Roles
{
    public ?\driver\pgsqlDriver $pgsqlInstance = null;
    public RoleService $roleService;

    public function initRoleService(){
        // Initialize RoleService with the pgsqlDriver instance
        $this->roleService = new RoleService($this->pgsqlInstance);

    }

    public function getDefaultHomePageByID($roleID){
        return $this->roleService->fetchDefaultHomePageByID($roleID)['default_home'];
    }

    public function updateUserRole($account_id, $role_id, $userModified){
        $this->roleService->changeUserRole($account_id, $role_id, $userModified);
    }

    public function newUserRole($account_id, $role_id, $userCreated){
        $this->roleService->insertUserRole($account_id, $role_id, $userCreated);
    }

    public function fetchRoles(){
        return $this->roleService->getRoles();
    }

    public function getRoleByID($roleID){
        return $this->roleService->fetchRoleByID($roleID);
    }
    
    public function newRole($roleName, $userCreated, $defaultHome){
        $this->roleService->insertRole($roleName, $userCreated, $defaultHome);
    }

    public function deleteRole($role_id){
        $this->roleService->removeRole($role_id);
    }

    // public function getRoles(){
    //     return $this->roles;
    // }

    // public function setRoles($roles){
    //     $this->roles = $roles;
    // }
}