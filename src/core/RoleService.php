<?php

namespace jdTonido\RBAC\core;

use jdTonido\RBAC\Enums\TableNames;
use jdTonido\RBAC\Helpers\PgsqlResultConverter;

class RoleService{

    public function __construct(public $pgsqlInstance){}

    public function getRoles(){
        $rolesRes = $this->pgsqlInstance->select()
            ->from(TableNames::Roles->value)
            ->readData();

        return PgsqlResultConverter::array($rolesRes);
        // while($result = pg_fetch_assoc($rolesRes)){
        //     $roles[] = $result;
        // }

        // return $roles;
    }

    public function fetchDefaultHomePageByID($roleID){
        $results = $this->pgsqlInstance->select('default_home')
            ->from(TableNames::Roles->value)
            ->where("role_id = '$roleID'")
            ->readData();

        return PgsqlResultConverter::array($results)[0];
    }

    public function fetchRoleByID($roleID){
        $results = $this->pgsqlInstance->select()
            ->from(TableNames::Roles->value)
            ->where("role_id = '$roleID'")
            ->readData();

        return PgsqlResultConverter::array($results)[0];
    }

    public function getRoleByAccountID($accountID){
        return $this->pgsqlInstance->select()
            ->from(TableNames::Roles->value.' r')
            ->join(Tablenames::UserHasRoles->value.' uhr', 'r.role_id = uhr.role_id','inner')
            ->where('account_id ='.$accountID)
            ->readData();
        
    }
    
    //name the mothod assignRoleToUser for traits
    public function insertUserRole($account_id, $role_id, $userCreated){
       
        $this->pgsqlInstance->beginTransaction();
        try{
            $this->pgsqlInstance->table(TableNames::UserHasRoles->value)
				->insert_set('account_id',$account_id)
				->insert_set('role_id',$role_id)
				->insert_set('user_created',$userCreated)
				->insertData();

            $this->pgsqlInstance->commit();
		} catch (\Exception $e) {
            $this->pgsqlInstance->rollback();

			throw new \Exception($e->getMessage());
        }
    }

    //name the mothod reassignRoleToUser for traits
    public function changeUserRole($account_id, $role_id, $userModified){
        $this->pgsqlInstance->beginTransaction();
        try{
            $this->pgsqlInstance->table(TableNames::UserHasRoles->value)
                ->setPrimaryKey('account_id')
                ->setPrimaryID($account_id)
                ->update_set('role_id',$role_id) //date("Y-m-d h:i:s a", time()))
                ->update_set('user_modified', $userModified)
                ->updateData()
                ;
            $this->pgsqlInstance->commit();
        } catch (\Exception $e) {
            $this->pgsqlInstance->rollback();

            throw new \Exception($e->getMessage());
        }
    }

    //name the mothod removeUserRole for traits
    public function detachUserRole($account_id){
        $this->pgsqlInstance->beginTransaction();
        try{
            $this->pgsqlInstance->deleteData(TableNames::UserHasRoles->value,'acount_id='.$account_id);
                
            $this->pgsqlInstance->commit();
        } catch (\Exception $e) {
            $this->pgsqlInstance->rollback();

            throw new \Exception($e->getMessage());
        }
    }

    public function insertRole($roleName, $userCreated, $defaultHome){
        $this->pgsqlInstance->beginTransaction();
        try{
            $this->pgsqlInstance->table(TableNames::Roles->value)
				->insert_set('name',$roleName)
                ->insert_set('user_created',$userCreated)
                ->insert_set('default_home',$defaultHome)
				->insertData();

            $this->pgsqlInstance->commit();
		} catch (\Exception $e) {
            $this->pgsqlInstance->rollback();

			throw new \Exception($e->getMessage());
        }
    }

    //name the mothod removeUserRole for traits
    public function removeRole($role_id){
        $this->pgsqlInstance->beginTransaction();
        try{
            $this->pgsqlInstance->deleteData(TableNames::Roles->value,'role_id='.$role_id);
                
            $this->pgsqlInstance->commit();
        } catch (\Exception $e) {
            $this->pgsqlInstance->rollback();

            throw new \Exception($e->getMessage());
        }
    }
}