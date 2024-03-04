<?php

namespace Entities\Companies\Classes\Departments;

use Entities\Companies\Models\Departments\DepartmentUserRelModel;

class DepartmentUserRels extends AppEntity
{
    public string $strEntityName       = "Companies";
    public $strDatabaseTable    = "company_department_user_rel";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = DepartmentUserRelModel::class;
    public $strMainModelPrimary = "department_user_rel_id";
}