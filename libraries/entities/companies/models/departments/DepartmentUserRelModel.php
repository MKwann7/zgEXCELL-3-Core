<?php

namespace Entities\Companies\Models\Departments;

use App\Core\AppModel;

class DepartmentUserRelModel extends AppModel
{
    protected $EntityName = "Company";
    protected $ModelName = "CompanyDepartmentUserRel";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "department_user_rel_id" => ["type" => "int","length" => 15],
            "company_id" => ["type" => "int","length" => 15],
            "division_id" => ["type" => "int","length" => 15],
            "department_id" => ["type" => "int","length" => 15],
            "user_id" => ["type" => "int","length" => 15],
            "created_on" => ["type" => "datetime"],
            "last_updated" => ["type" => "datetime"],
            "sys_row_id" => ["type" => "char","length" => 36]
        ];
    }
}