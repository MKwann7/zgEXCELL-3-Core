<?php

namespace Entities\Companies\Models\Departments;

use App\Core\AppModel;

class DepartmentModel extends AppModel
{
    protected $EntityName = "Company";
    protected $ModelName = "CompanyDepartment";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "company_department_id" => ["type" => "int","length" => 15],
            "company_id" => ["type" => "int","length" => 15],
            "division_id" => ["type" => "int","length" => 15],
            "department_class_id" => ["type" => "int","length" => 15],
            "department_type_id" => ["type" => "int","length" => 15],
            "department_type_id" => ["type" => "int","length" => 15],
            "parent_department_id" => ["type" => "int","length" => 15],
            "name" => ["type" => "varchar","length" => 35],
            "label" => ["type" => "varchar","length" => 25],
            "description" => ["type" => "varchar","length" => 500],
            "parent_id" => ["type" => "int","length" => 15],
            "receives_promo_card" => ["type" => "boolean"],
            "promo_card_per_user" => ["type" => "int", "length" => 3],
            "can_receive_tickets" => ["type" => "boolean"],
            "can_create_tickets" => ["type" => "boolean"],
            "can_edit_tickets" => ["type" => "boolean"],
            "can_delete_tickets" => ["type" => "boolean"],
            "can_view_customers" => ["type" => "boolean"],
            "can_create_customers" => ["type" => "boolean"],
            "can_edit_customers" => ["type" => "boolean"],
            "can_delete_customers" => ["type" => "boolean"],
            "can_view_users" => ["type" => "boolean"],
            "can_create_users" => ["type" => "boolean"],
            "can_edit_users" => ["type" => "boolean"],
            "can_delete_users" => ["type" => "boolean"],
            "can_view_cards" => ["type" => "boolean"],
            "can_purchase_cards" => ["type" => "boolean"],
            "can_edit_cards" => ["type" => "boolean"],
            "can_delete_cards" => ["type" => "boolean"],
            "can_view_tool_owners" => ["type" => "boolean"],
            "can_edit_tool_owners" => ["type" => "boolean"],
            "can_delete_tool_owners" => ["type" => "boolean"],
            "can_view_tools" => ["type" => "boolean"],
            "can_create_tools" => ["type" => "boolean"],
            "can_edit_tools" => ["type" => "boolean"],
            "can_delete_tools" => ["type" => "boolean"],
            "can_view_packages" => ["type" => "boolean"],
            "can_create_packages" => ["type" => "boolean"],
            "can_edit_packages" => ["type" => "boolean"],
            "can_delete_packages" => ["type" => "boolean"],
            "sys_row_id" => ["type" => "char","length" => 36]
        ];
    }
}