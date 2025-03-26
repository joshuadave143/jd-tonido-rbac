<?php

namespace jdTonido\RBAC\core;

use jdTonido\RBAC\Enums\Permissions;
use jdTonido\RBAC\Enums\TableNames;
use jdTonido\RBAC\helpers\DynamicMethodWrapperForPermission;
use jdTonido\RBAC\helpers\NestedDynamicMethodWrapperForPermission;
use jdTonido\RBAC\helpers\PgsqlResultConverter;

class PermissionsService{
    /**
     * todo implement
     * show all modules
     * register modules using functions and replace using function with second parameter
     * 
     */
    public function __construct(public $pgsqlInstance){}

    public function getPermissions(){
        $results = $this->pgsqlInstance->select()
            ->from(TableNames::Permissions->value)
            ->readData();

        return PgsqlResultConverter::array($results);
    }

    public function getPermissionsByRoleIdAndModuleName($roleID, $moduleName){
        $results = $this->pgsqlInstance->select('p.name')
            ->from(TableNames::ModuleHasPermissions->value.' mhp')
            ->join(TableNames::Modules->value.' m',' mhp.module_id = m.module_id','left')
            ->join(TableNames::Permissions->value.' p','mhp.permission_id = p.permission_id','left')
            ->where("mhp.role_id = ".$roleID." AND m.name = '".$moduleName."'")
            ->readData();

        $permissionsArray = PgsqlResultConverter::array($results);
        $permissionsArray = array_combine(array_column($permissionsArray, "name"), array_column($permissionsArray, "name"));
        
        return new DynamicMethodWrapperForPermission($permissionsArray,$this->pgsqlInstance);
    }

    public function getPermissionsByRoleIdAndReportName($roleID, $reportName){
        $results = $this->pgsqlInstance->select('p.name')
            ->from(TableNames::ReportHasPermissions->value.' rhp')
            ->join(TableNames::Reports->value.' r',' rhp.report_id = r.report_id','left')
            ->join(TableNames::Permissions->value.' p','rhp.permission_id = p.permission_id','left')
            ->where("rhp.role_id = ".$roleID." AND r.report_name = '".$reportName."'")
            ->readData();

        $permissionsArray = PgsqlResultConverter::array($results);
        $permissionsArray = array_combine(array_column($permissionsArray, "name"), array_column($permissionsArray, "name"));
        
        return new DynamicMethodWrapperForPermission($permissionsArray,$this->pgsqlInstance);
    }

    public function getReportsPermissionsByRoleId($roleID){
        $results = $this->pgsqlInstance->select('r.report_name as mr_name,p.name')
            ->from(TableNames::ReportHasPermissions->value.' rhp')
            ->join(TableNames::Reports->value.' r',' rhp.report_id = r.report_id','left')
            ->join(TableNames::Permissions->value.' p','rhp.permission_id = p.permission_id','left')
            ->where("rhp.role_id = ".$roleID)
            ->readData();

        $permissionsArray = PgsqlResultConverter::array($results);

        return new NestedDynamicMethodWrapperForPermission($permissionsArray,$this->pgsqlInstance);
    }

    public function getModulesPermissionsByRoleId($roleID){
        $results = $this->pgsqlInstance->select('m.name as mr_name,p.name')
            ->from(TableNames::ModuleHasPermissions->value.' mhp')
            ->join(TableNames::Modules->value.' m',' mhp.module_id = m.module_id','left')
            ->join(TableNames::Permissions->value.' p','mhp.permission_id = p.permission_id','left')
            ->where("mhp.role_id = ".$roleID)
            ->readData();

        $permissionsArray = PgsqlResultConverter::array($results);
        // return $permissionsArray;
        return new NestedDynamicMethodWrapperForPermission($permissionsArray);
    }

    public function fetchPermissionIDByName($permissionName){
        $results = $this->pgsqlInstance->select()
            ->from(TableNames::Permissions->value)
            ->where("name = '$permissionName'")
            ->readData();

        return PgsqlResultConverter::array($results)[0];
    }
    

    public function fetchPermissionByID($permissionID){
        $results = $this->pgsqlInstance->select()
            ->from(TableNames::Permissions->value)
            ->where("permission_id = '$permissionID'")
            ->readData();

        return PgsqlResultConverter::array($results)[0];
    }

    public function hasPermissionForModule($roleID, $permissionID, $moduleID){
        return $this->pgsqlInstance->select()
            ->from(TableNames::ModuleHasPermissions->value)
            ->where("role_id = $roleID
                    and permission_id = $permissionID
                    and module_id = $moduleID"
                    )
            ->readData()?true:false;
    }

    public function hasPermissionForReport($roleID, $permissionID, $reportID){
        return $this->pgsqlInstance->select()
            ->from(TableNames::ReportHasPermissions->value)
            ->where("role_id = $roleID
                    and permission_id = $permissionID
                    and report_id = $reportID"
                    )
            ->readData()?true:false;
    }

    public function getPermissionByModuleIDAndPermissionName($moduleID, $permissionName){
        $results = $this->pgsqlInstance->select()
            ->from(TableNames::ModuleHasPermissions->value)
            ->join(TableNames::Permissions->value,
                TableNames::ModuleHasPermissions->value.'.permission_id = '.TableNames::Permissions->value.'.permission_id','left')
            ->where('module_id = '.$moduleID.
                    " and name = '$permissionName'"
                    )
            ->readData();

        return PgsqlResultConverter::array($results);
    }

    public function allowModulePermission($roleID, $permissionID, $moduleID, $userCreated){
       
        $this->pgsqlInstance->beginTransaction();
        try{
            $permissionViewID = $this->fetchPermissionIDByName(Permissions::View->value)['permission_id'];

            switch ($this->fetchPermissionByID($permissionID)['name']) {
                case Permissions::Add->value:
                case Permissions::Edit->value:
                case Permissions::Delete->value:
                    if( $this->hasPermissionForModule($roleID, $permissionViewID, $moduleID)){
                        break;
                    }
                    $this->pgsqlInstance->table(TableNames::ModuleHasPermissions->value)
                    ->insert_set('role_id',$roleID)
                    ->insert_set('permission_id',$permissionViewID)
                    ->insert_set('module_id',$moduleID)
                    ->insert_set('user_created',$userCreated)
                    ->insertData();
                    break;
            }
            $this->pgsqlInstance->table(TableNames::ModuleHasPermissions->value)
				->insert_set('role_id',$roleID)
				->insert_set('permission_id',$permissionID)
				->insert_set('module_id',$moduleID)
                ->insert_set('user_created',$userCreated)
				->insertData();

            $this->pgsqlInstance->commit();
		} catch (\Exception $e) {
            $this->pgsqlInstance->rollback();
			throw new \Exception($e->getMessage());
        }
    }

    public function denyModulePermission($roleID, $permissionID, $moduleID){
        $this->pgsqlInstance->beginTransaction();
        try{

            if( $this->fetchPermissionByID($permissionID)['name'] == Permissions::View->value){
                $this->pgsqlInstance->deleteData(TableNames::ModuleHasPermissions->value,
                    'role_id='.$roleID.' and module_id='.$moduleID
                );
            }
            else{
                $this->pgsqlInstance->deleteData(TableNames::ModuleHasPermissions->value,
                    'role_id='.$roleID.' and permission_id='.$permissionID.' and module_id='.$moduleID
                );
            }

            

            $this->pgsqlInstance->commit();
        } catch (\Exception $e) {
            $this->pgsqlInstance->rollback();
            throw new \Exception($e->getMessage());
        }
    } 

    public function allowModulePermissions($permissionName, $roleID, $userCreated){
       
        $this->pgsqlInstance->beginTransaction();
        try{

            $modules = (new ModuleService($this->pgsqlInstance))->getModules();
            
            $permissionViewID = $this->fetchPermissionIDByName(Permissions::View->value)['permission_id'];
            $permissionID = $this->fetchPermissionIDByName($permissionName)['permission_id'];

            switch ($permissionName) {
                case Permissions::Add->value:
                case Permissions::Edit->value:
                case Permissions::Delete->value:
                    foreach($modules as $module){
                        if( $this->hasPermissionForModule($roleID, $permissionViewID, $module['module_id'])){
                            continue;
                        }
                        $this->pgsqlInstance->table(TableNames::ModuleHasPermissions->value)
                        ->insert_set('role_id',$roleID)
                        ->insert_set('permission_id',$permissionViewID)
                        ->insert_set('module_id',$module['module_id'])
                        ->insert_set('user_created',$userCreated)
                        ->insertData();
                    }
                    break;
            }
            foreach($modules as $module){
                if( $this->hasPermissionForModule($roleID, $permissionID, $module['module_id'])){
                    continue;
                }
                $this->pgsqlInstance->table(TableNames::ModuleHasPermissions->value)
				->insert_set('role_id',$roleID)
				->insert_set('permission_id',$permissionID)
				->insert_set('module_id',$module['module_id'])
                ->insert_set('user_created',$userCreated)
				->insertData();
            }
            

            $this->pgsqlInstance->commit();
		} catch (\Exception $e) {
            $this->pgsqlInstance->rollback();
			throw new \Exception($e->getMessage());
        }
    }

    public function denyModulePermissions($permissionName, $roleID){
        $this->pgsqlInstance->beginTransaction();
        try{
            $modules = (new ModuleService($this->pgsqlInstance))->getModules();
            $permissionID = $this->fetchPermissionIDByName($permissionName)['permission_id'];

            foreach($modules as $module){
                
                if( $this->fetchPermissionByID($permissionID)['name'] == Permissions::View->value){
                    $this->pgsqlInstance->deleteData(TableNames::ModuleHasPermissions->value,
                        'role_id='.$roleID.' and module_id='.$module['module_id']
                    );
                }
                else{
                    $this->pgsqlInstance->deleteData(TableNames::ModuleHasPermissions->value,
                        'role_id='.$roleID.' and permission_id='.$permissionID.' and module_id='.$module['module_id']
                    );
               
                }
            
            }

            

            $this->pgsqlInstance->commit();
        } catch (\Exception $e) {
            $this->pgsqlInstance->rollback();
            throw new \Exception($e->getMessage());
        }
    }

    public function allowReportPermission($roleID, $permissionID, $reportID, $userCreated){
        $this->pgsqlInstance->beginTransaction();
        try{
                        
            $this->pgsqlInstance->table(TableNames::ReportHasPermissions->value)
				->insert_set('role_id',$roleID)
				->insert_set('permission_id',$permissionID)
				->insert_set('report_id',$reportID)
                ->insert_set('user_created',$userCreated)
				->insertData();

            $this->pgsqlInstance->commit();
		} catch (\Exception $e) {
            $this->pgsqlInstance->rollback();
			throw new \Exception($e->getMessage());
        }  
    }

    public function denyReportPermission($roleID, $permissionID, $reportID){
        $this->pgsqlInstance->beginTransaction();
        try{
            $this->pgsqlInstance->deleteData(TableNames::ReportHasPermissions->value,
                'role_id='.$roleID.' and permission_id='.$permissionID.' and report_id='.$reportID
            );

            $this->pgsqlInstance->commit();
        } catch (\Exception $e) {
            $this->pgsqlInstance->rollback();
            throw new \Exception($e->getMessage());
        }
    }

    public function allowReportPermissions($permissionName, $roleID, $userCreated){
        $this->pgsqlInstance->beginTransaction();
        try{

            $reports = (new ReportService($this->pgsqlInstance))->getReports();
            
            
            $permissionID = $this->fetchPermissionIDByName($permissionName)['permission_id'];

            foreach($reports as $report){
                if( $this->hasPermissionForReport($roleID, $permissionID, $report['report_id'])){
                    continue;
                }
                $this->pgsqlInstance->table(TableNames::ReportHasPermissions->value)
				->insert_set('role_id',$roleID)
				->insert_set('permission_id',$permissionID)
				->insert_set('report_id',$report['report_id'])
                ->insert_set('user_created',$userCreated)
				->insertData();
            }
            

            $this->pgsqlInstance->commit();
		} catch (\Exception $e) {
            $this->pgsqlInstance->rollback();
			throw new \Exception($e->getMessage());
        }
    }

    public function denyReportPermissions($permissionName, $roleID){
        $this->pgsqlInstance->beginTransaction();
        try{
            $reports = (new ReportService($this->pgsqlInstance))->getReports();
            $permissionID = $this->fetchPermissionIDByName($permissionName)['permission_id'];

            foreach($reports as $report){
                
                $this->pgsqlInstance->deleteData(TableNames::ReportHasPermissions->value,
                    'role_id='.$roleID.' and permission_id='.$permissionID.' and report_id='.$report['report_id']
                );            
            }
            $this->pgsqlInstance->commit();
        } catch (\Exception $e) {
            $this->pgsqlInstance->rollback();
            throw new \Exception($e->getMessage());
        }
    }
}