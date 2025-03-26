<?php

namespace jdTonido\RBAC\core;

use jdTonido\RBAC\Enums\TableNames;
use jdTonido\RBAC\helpers\PgsqlResultConverter;

class ReportService{
    /**
     * todo implement
     * show all modules
     * register modules using functions and replace using function with second parameter
     * 
     */
    public function __construct(public $pgsqlInstance){}

    public function getReports(){
        $results = $this->pgsqlInstance->select()
            ->from(TableNames::Reports->value)
            ->readData();

        return PgsqlResultConverter::array($results);
    }

    public function fetchReportsWithPermissionsByRole($role_id){
        $results = $this->pgsqlInstance->select()
            ->from(TableNames::Reports->value.' r')
            ->join(TableNames::ReportHasPermissions->value.' rp', 'r.report_id = rp.report_id','left')
            ->join(TableNames::Permissions->value.' p', 'p.permission_id = rp.permission_id','left')
            ->where('rp.role_id='.$role_id)
            ->readData();

        return PgsqlResultConverter::array($results);
    }

    public function fetchReportByName($reportName){
        $results = $this->pgsqlInstance->select()
        ->from(TableNames::Reports->value)
        ->where("report_name = '$reportName'")
        ->readData();
    
    if(!$results) return [];
    return PgsqlResultConverter::array($results)[0];
    }

    public function fetchReportByID($reportID){
        
        $results = $this->pgsqlInstance->select()
            ->from(TableNames::Reports->value)
            ->where("report_id = '$reportID'")
            ->readData();

        return PgsqlResultConverter::array($results)[0];
    }

    public function insertModule($reportName){
       
        $this->pgsqlInstance->beginTransaction();
        try{
            $this->pgsqlInstance->table(TableNames::Reports->value)
				->insert_set('report_name',$reportName)
				->insertData();

            $this->pgsqlInstance->commit();
		} catch (\Exception $e) {
            $this->pgsqlInstance->rollback();

			throw new \Exception($e->getMessage());
        }
    }
}