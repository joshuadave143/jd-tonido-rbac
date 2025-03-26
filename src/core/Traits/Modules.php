<?php
namespace jdTonido\RBAC\core\Traits;

use jdTonido\RBAC\core\ModuleService;

trait Modules
{
     public ?\driver\pgsqlDriver $pgsqlInstance = null;
     public ModuleService $moduleService;

     private $moduleName;
     private $url = '';

     public function initModuleService(){
          // Initialize RoleService with the pgsqlDriver instance
          $this->moduleService = new ModuleService($this->pgsqlInstance);

     }

     public function fetchModules() {
          
          return $this->moduleService->getModules();
     }

     public function getListURL(){
          return $this->moduleService->fetchURL();
     }

     public function getModulesWithPermissionsByRole($role_id){
          return $this->moduleService->fetchModulesWithPermissionsByRole($role_id);
     }

     public function getModuleByID($moduleId){
          return $this->moduleService->fetchModuleByID($moduleId);
     }

     public function registerURL($url){
          $this->url = $url;
          return $this;
     }

     public function registerModule($moduleName){
          try{
               $this->moduleName = $moduleName;

               if( !$this->moduleService->fetchModuleByName($moduleName) ){
                    $this->moduleService->insertModule($moduleName, $this->url);
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