<?php

namespace jdTonido\RBAC\Enums;

enum TableNames: string{
    case Modules                = 'rbac_modules';
    case Permissions            = 'rbac_permissions';
    case Reports                = 'rbac_reports';
    case Roles                  = 'rbac_roles';
    case UserHasRoles           = 'rbac_user_has_roles';
    case ModuleHasPermissions   = 'rbac_module_has_permissions';
    case ReportHasPermissions   = 'rbac_report_has_permissions';
}