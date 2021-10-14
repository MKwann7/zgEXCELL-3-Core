<?php

namespace Entities\Companies\Models\Departments;

use App\Core\AppModel;

class DepartmentTicketQueueModel extends AppModel
{
    protected $EntityName = "Company";
    protected $ModelName = "CompanyDepartmentTicketQueue";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "ticket_queue_id" => ["type" => "int","length" => 15],
            "company_id" => ["type" => "int","length" => 15],
            "division_id" => ["type" => "int","length" => 15],
            "company_department_id" => ["type" => "int","length" => 15],
            "queue_type_id" => ["type" => "int","length" => 5],
            "label" => ["type" => "varchar","length" => 50],
            "name" => ["type" => "varchar","length" => 50],
            "description" => ["type" => "varchar","length" => 250],
            "created_on" => ["type" => "datetime"],
            "last_updated" => ["type" => "datetime"],
            "sys_row_id" => ["type" => "char","length" => 36]
        ];
    }
}