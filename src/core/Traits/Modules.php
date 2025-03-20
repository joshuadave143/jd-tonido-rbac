<?php
namespace jdTonido\RBAC\core\Traits;

use jdTonido\RBAC\core\ModuleService;
use jdTonido\RBAC\core\RoleService;
use jdTonido\RBAC\Exceptions\InvalidPGSQLInstance;
use jdTonido\RBAC\Factories\PgsqlDriverFactory;

trait Modules
{
    public ?\driver\pgsqlDriver $pgsqlInstance = null;
    public ModuleService $moduleService;

    private $moduleName;

    public function initModuleService(){
        // Initialize RoleService with the pgsqlDriver instance
        $this->moduleService = new ModuleService($this->pgsqlInstance);

    }

   public function fetchModules() {
        
        return $this->moduleService->getModules();
   }

   public function getModulesWithPermissionsByRole($role_id){
        return $this->moduleService->fetchModulesWithPermissionsByRole($role_id);
   }

   public function getModuleByID($moduleId){
        return $this->moduleService->fetchModuleByID($moduleId);
   }

   public function registerModule($moduleName){
     try{
          $this->moduleName = $moduleName;

          if( !$this->moduleService->fetchModuleByName($moduleName) ){
               $this->moduleService->insertModule($moduleName);
          }
     }
     catch(\Exception $e){
          throw new \Exception($e->getMessage(), 405); 
     }
    
   }

   public function getRegisterModuleName(){
     return $this->moduleName;
   }
}