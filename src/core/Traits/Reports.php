<?php
namespace jdTonido\RBAC\core\Traits;

use jdTonido\RBAC\core\ReportService;

trait Reports
{
     public ?\driver\pgsqlDriver $pgsqlInstance = null;
     public ReportService $reportService;
     private $reportName;
     public function initReportService(){
          // Initialize RoleService with the pgsqlDriver instance
          $this->reportService = new ReportService($this->pgsqlInstance);

     }

     public function fetchReports() {
          
          return $this->reportService->getReports();
     }

     public function getReportsWithPermissionsByRole($role_id){
          return $this->reportService->fetchReportsWithPermissionsByRole($role_id);
     }

     public function getReportByID($reportID){
          return $this->reportService->fetchReportByID($reportID);
     }

     public function registerReport($reportName){
          try{
               $this->reportName = $reportName;

               if( !$this->reportService->fetchReportByName($reportName) ){
                    $this->reportService->insertModule($reportName);
               }
          }
          catch(\Exception $e){
               throw new \Exception($e->getMessage(), 405); 
          }
     
     }

     public function getRegisterReportName(){
          return $this->reportName;
     }
}