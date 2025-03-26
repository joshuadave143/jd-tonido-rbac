<?php
namespace jdTonido\RBAC\core\Traits;

use jdTonido\RBAC\core\PermissionsService;

trait HasPermissions
{
    public ?\driver\pgsqlDriver $pgsqlInstance = null;
    public PermissionsService $permissionsService;

    
    public function initPermissionsService(){
        // Initialize RoleService with the pgsqlDriver instance
        $this->permissionsService = new PermissionsService($this->pgsqlInstance);
    }

    public function hasModulePermission($module_id, $permissionName){
        return  $this->permissionsService->getPermissionByModuleIDAndPermissionName($module_id, $permissionName)?true:false;
    }

    public function modulesPermissions($roleID, $moduleName){
        return $this->permissionsService->getPermissionsByRoleIdAndModuleName($roleID, $moduleName);        
    }

    public function reportsPermissions($roleID, $reportName){
        return $this->permissionsService->getPermissionsByRoleIdAndReportName($roleID, $reportName);        
    }

    public function getPermissionIDByName($permissionName){
        return $this->permissionsService->fetchPermissionIDByName($permissionName);
    }

    public function getPermissionByID($permissionName){
        return $this->permissionsService->fetchPermissionByID($permissionName);
    }

    public function getReportsWithPermissionsByRoleID($roleID){
        return $this->permissionsService->getReportsPermissionsByRoleId($roleID);
    }

    public function getModulesWithPermissionsByRoleID($roleID){
        return $this->permissionsService->getModulesPermissionsByRoleId($roleID);
    }

    public function processModulePermission($roleID, $permissionID, $moduleID, $isAllow = true, $userCreated){
       
        if($isAllow){
            return $this->permissionsService->allowModulePermission($roleID, $permissionID, $moduleID, $userCreated);
        }

        return $this->permissionsService->denyModulePermission($roleID, $permissionID, $moduleID, $userCreated);
    }

    public function processModulePermissions($permissionName, $roleID, $isAllow = true, $userCreated){
       
        if($isAllow){
            return $this->permissionsService->allowModulePermissions($permissionName, $roleID, $userCreated);
        }

        return $this->permissionsService->denyModulePermissions($permissionName, $roleID, $userCreated);
    }

    public function processReportPermission($roleID, $permissionID, $reportID, $isAllow = true, $userCreated){
       
        if($isAllow){
            return $this->permissionsService->allowReportPermission($roleID, $permissionID, $reportID, $userCreated);
        }

        return $this->permissionsService->denyReportPermission ($roleID, $permissionID, $reportID, $userCreated);
    }

    public function processReportPermissions($permissionName, $roleID, $isAllow = true, $userCreated){
       
        if($isAllow){
            return $this->permissionsService->allowReportPermissions($permissionName, $roleID, $userCreated);
        }

        return $this->permissionsService->denyReportPermissions($permissionName, $roleID, $userCreated);
    }
}