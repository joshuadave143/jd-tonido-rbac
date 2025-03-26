<?php

namespace jdTonido\RBAC\core;

use jdTonido\RBAC\Enums\TableNames;
use jdTonido\RBAC\helpers\PgsqlResultConverter;

class ModuleService{
    /**
     * todo implement
     * show all modules
     * register modules using functions and replace using function with second parameter
     * 
     */
    public function __construct(public $pgsqlInstance){}

    public function getModules(){
        $results = $this->pgsqlInstance->select()
            ->from(TableNames::Modules->value)
            ->readData();

        return PgsqlResultConverter::array($results);
    }

    public function fetchModulesWithPermissionsByRole($role_id){
        $results = $this->pgsqlInstance->select()
            ->from(TableNames::Modules->value.' m')
            ->join(TableNames::ModuleHasPermissions->value.' mhp', 'm.module_id = mhp.module_id','left')
            ->join(TableNames::Permissions->value.' p', 'mhp.permission_id = p.permission_id','left')
            ->where('mhp.role_id='.$role_id)
            ->readData();

        return PgsqlResultConverter::array($results);
    }

    public function fetchModuleByID($moduleID){
        $results = $this->pgsqlInstance->select()
            ->from(TableNames::Modules->value)
            ->where("module_id = '$moduleID'")
            ->readData();

        return PgsqlResultConverter::array($results)[0];
    }

    public function fetchModuleByName($moduleName){
        $results = $this->pgsqlInstance->select()
            ->from(TableNames::Modules->value)
            ->where("name = '$moduleName'")
            ->readData();
        
        if(!$results) return [];
        return PgsqlResultConverter::array($results)[0];
    }

    public function insertModule($moduleName, $url){
       
        $this->pgsqlInstance->beginTransaction();
        try{
            $this->pgsqlInstance->table(TableNames::Modules->value)
				->insert_set('name',$moduleName)
				->insert_set('url',$url)
				->insertData();

            $this->pgsqlInstance->commit();
		} catch (\Exception $e) {
            $this->pgsqlInstance->rollback();

			throw new \Exception($e->getMessage());
        }
    }

    public function fetchURL(){
        $results = $this->pgsqlInstance->select('url')
            ->from(TableNames::Modules->value)
            ->where('url is not null')
            ->readData();

        return PgsqlResultConverter::array($results);
    }

    
}