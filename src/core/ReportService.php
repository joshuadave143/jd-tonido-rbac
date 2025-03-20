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

    public function fetchReportByID($reportID){
        
        $results = $this->pgsqlInstance->select()
            ->from(TableNames::Reports->value)
            ->where("report_id = '$reportID'")
            ->readData();

        return PgsqlResultConverter::array($results)[0];
    }

    // public function getRoleByAccountID($accountID){
    //     return $this->pgsqlInstance->select()
    //         ->from(TableNames::Roles->value.' r')
    //         ->join(Tablenames::UserHasRoles->value.' uhr', 'r.role_id = uhr.role_id','inner')
    //         ->where('account_id ='.$accountID)
    //         ->readData();
        
    // }
    
    // //name the mothod assignRoleToUser for traits
    // public function insertUserRole($account_id, $role_id){
       
    //     $this->pgsqlInstance->beginTransaction();
    //     try{
    //         $this->pgsqlInstance->table(TableNames::UserHasRoles->value)
	// 			->insert_set('account_id',$account_id)
	// 			->insert_set('role_id',$role_id)
	// 			->insertData();

    //         $this->pgsqlInstance->commit();
	// 	} catch (\Exception $e) {
    //         $this->pgsqlInstance->rollback();

	// 		throw new \Exception($e->getMessage());
    //     }
    // }

    // //name the mothod reassignRoleToUser for traits
    // public function changeUserRole($account_id, $role_id){
    //     $this->pgsqlInstance->beginTransaction();
    //     try{
    //         $this->pgsqlInstance->table(TableNames::UserHasRoles->value)
    //             ->setPrimaryKey('account_id')
    //             ->setPrimaryID($account_id)
    //             ->update_set('role_id',$role_id) //date("Y-m-d h:i:s a", time()))
    //             ->updateData()
    //             ;
    //         $this->pgsqlInstance->commit();
    //     } catch (\Exception $e) {
    //         $this->pgsqlInstance->rollback();

    //         throw new \Exception($e->getMessage());
    //     }
    // }

    // //name the mothod removeUserRole for traits
    // public function detachUserRole($account_id){
    //     $this->pgsqlInstance->beginTransaction();
    //     try{
    //         $this->pgsqlInstance->deleteData(TableNames::UserHasRoles->value,'acount_id='.$account_id);
                
    //         $this->pgsqlInstance->commit();
    //     } catch (\Exception $e) {
    //         $this->pgsqlInstance->rollback();

    //         throw new \Exception($e->getMessage());
    //     }
    // }

    // public function insertRole($roleName){
    //     $this->pgsqlInstance->beginTransaction();
    //     try{
    //         $this->pgsqlInstance->table(TableNames::Roles->value)
	// 			->insert_set('name',$roleName)
	// 			->insertData();

    //         $this->pgsqlInstance->commit();
	// 	} catch (\Exception $e) {
    //         $this->pgsqlInstance->rollback();

	// 		throw new \Exception($e->getMessage());
    //     }
    // }

    // //name the mothod removeUserRole for traits
    // public function removeRole($role_id){
    //     $this->pgsqlInstance->beginTransaction();
    //     try{
    //         $this->pgsqlInstance->deleteData(TableNames::Roles->value,'role_id='.$role_id);
                
    //         $this->pgsqlInstance->commit();
    //     } catch (\Exception $e) {
    //         $this->pgsqlInstance->rollback();

    //         throw new \Exception($e->getMessage());
    //     }
    // }
}