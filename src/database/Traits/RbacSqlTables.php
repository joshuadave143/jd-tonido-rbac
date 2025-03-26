<?php

namespace jdTonido\RBAC\database\Traits;

trait RbacSqlTables{
    public function rbac_roles(){
        return '
            CREATE TABLE IF NOT EXISTS "rbac_roles" (
                "role_id" SERIAL NOT NULL,
                "name" VARCHAR(50) NOT NULL UNIQUE,
                "default_home" VARCHAR(100) NULL DEFAULT NULL,
                "user_created" VARCHAR(50) NOT NULL,
                "date_created" TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY ("role_id")
            );
            COMMENT ON COLUMN "rbac_roles"."role_id" IS \'\';
            COMMENT ON COLUMN "rbac_roles"."name" IS \'\';
        ';
    }

    public function rbac_permissions(){
        return '
            CREATE TABLE IF NOT EXISTS "rbac_permissions" (
                "permission_id" SERIAL NOT NULL,
                "name" VARCHAR(50) NOT NULL,
                "type" VARCHAR(50) NOT NULL,
                PRIMARY KEY ("permission_id")
            )
            ;
            COMMENT ON COLUMN "rbac_permissions"."permission_id" IS \'\';
            COMMENT ON COLUMN "rbac_permissions"."name" IS \'\';
            COMMENT ON COLUMN "rbac_permissions"."type" IS \'report or module\';

        ';
    }

    public function rbac_reports(){
        return '
            CREATE TABLE IF NOT EXISTS "rbac_reports" (
                "report_id" SERIAL NOT NULL,
                "report_name" VARCHAR(100) NOT NULL,
                PRIMARY KEY ("report_id"),
                CONSTRAINT unique_report_name UNIQUE ("report_name")
            )
            ;
            COMMENT ON COLUMN "rbac_reports"."report_id" IS \'\';
            COMMENT ON COLUMN "rbac_reports"."report_name" IS \'\';

        ';
    }

    public function rbac_modules(){
        return '
            CREATE TABLE IF NOT EXISTS "rbac_modules" (
                "module_id" SERIAL NOT NULL,
                "name" VARCHAR(100) NOT NULL,
                "default_home" VARCHAR(100) NULL DEFAULT NULL,
                PRIMARY KEY ("module_id"),
                CONSTRAINT unique_module_name UNIQUE ("name")
            )
            ;
            COMMENT ON COLUMN "rbac_modules"."module_id" IS \'\';
            COMMENT ON COLUMN "rbac_modules"."name" IS \'\';

        ';
    }

    public function rbac_user_has_roles(){
        return '
            CREATE TABLE IF NOT EXISTS "rbac_user_has_roles" (
                "account_id" INTEGER NOT NULL,
                "role_id" INTEGER NOT NULL,
                "user_created" VARCHAR(50) NOT NULL,
                "date_created" TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                "user_modified" VARCHAR(50),
                "date_modified" TIMESTAMP,
                CONSTRAINT "FK_account_id" FOREIGN KEY ("account_id") REFERENCES "account" ("account_id") ON UPDATE NO ACTION ON DELETE CASCADE,
                CONSTRAINT "FK_role_id" FOREIGN KEY ("role_id") REFERENCES "rbac_roles" ("role_id") ON UPDATE NO ACTION ON DELETE CASCADE
            )
            ;
            COMMENT ON COLUMN "rbac_user_has_roles"."account_id" IS \'\';
            COMMENT ON COLUMN "rbac_user_has_roles"."role_id" IS \'\';

            CREATE OR REPLACE FUNCTION update_modified_column()
            RETURNS TRIGGER AS $$
            BEGIN
                NEW.date_modified = CURRENT_TIMESTAMP;
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;

            CREATE TRIGGER trigger_update_date_modified
            BEFORE UPDATE ON "rbac_user_has_roles"
            FOR EACH ROW
            EXECUTE FUNCTION update_modified_column();
        ';
    }

    public function rbac_module_has_permissions(){
        return '
            CREATE TABLE IF NOT EXISTS "rbac_module_has_permissions" (
                "role_id" INTEGER NOT NULL,
                "permission_id" INTEGER NOT NULL,
                "module_id" INTEGER NOT NULL,
                "user_created" VARCHAR(50) NOT NULL,
                "date_created" TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT "FK_module" FOREIGN KEY ("module_id") REFERENCES "rbac_modules" ("module_id") ON UPDATE NO ACTION ON DELETE CASCADE,
                CONSTRAINT "FK_permission" FOREIGN KEY ("permission_id") REFERENCES "rbac_permissions" ("permission_id") ON UPDATE NO ACTION ON DELETE CASCADE,
                CONSTRAINT "FK_role" FOREIGN KEY ("role_id") REFERENCES "rbac_roles" ("role_id") ON UPDATE NO ACTION ON DELETE CASCADE
            )
            ;
            COMMENT ON COLUMN "rbac_module_has_permissions"."role_id" IS \'\';
            COMMENT ON COLUMN "rbac_module_has_permissions"."permission_id" IS \'\';
            COMMENT ON COLUMN "rbac_module_has_permissions"."module_id" IS \'\';

        ';
    }

    public function rbac_report_has_permissions(){
        return '
            CREATE TABLE IF NOT EXISTS "rbac_report_has_permissions" (
                "role_id" INTEGER NOT NULL,
                "permission_id" INTEGER NOT NULL,
                "report_id" INTEGER NOT NULL,
                "user_created" VARCHAR(50) NOT NULL,
                "dateCreated" TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT "FK_permission" FOREIGN KEY ("permission_id") REFERENCES "rbac_permissions" ("permission_id") ON UPDATE NO ACTION ON DELETE CASCADE,
                CONSTRAINT "FK_report" FOREIGN KEY ("report_id") REFERENCES "rbac_reports" ("report_id") ON UPDATE NO ACTION ON DELETE CASCADE,
                CONSTRAINT "FK_role" FOREIGN KEY ("role_id") REFERENCES "rbac_roles" ("role_id") ON UPDATE NO ACTION ON DELETE CASCADE
            )
            ;
            COMMENT ON COLUMN "rbac_report_has_permissions"."role_id" IS \'\';
            COMMENT ON COLUMN "rbac_report_has_permissions"."permission_id" IS \'\';
            COMMENT ON COLUMN "rbac_report_has_permissions"."report_id" IS \'\';

        ';
    }
}