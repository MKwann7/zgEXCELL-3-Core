<?php

namespace Entities\Opportunity\Models;

use App\Core\AppModel;

class OpportunityModel extends AppModel
{
    protected $EntityName = "Opportunity";
    protected $ModelName = "Opportunity";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "opportunity_id" => ["type" => "int","length" => 15],
            "user_id" => ["type" => "int","length" => 15,"fk" => ["table" => "user","key" => "user_id","value" => "username"]],
            "owner_id" => ["type" => "int","length" => 15,"fk" => ["table" => "user","key" => "user_id","value" => "username"]],
            "division_id" => ["type" => "int","length" => 15],
            "company_id" => ["type" => "int","length" => 15],
            "campaign_id" => ["type" => "int","length" => 15],
            "creator_id" => ["type" => "int","length" => 15,"fk" => ["table" => "user","key" => "user_id","value" => "username"]],
            "created_on" => ["type" => "datetime"],
            "created_by" => ["type" => "int","length" => 15,"fk" => ["table" => "user","key" => "user_id","value" => "username"]],
            "last_updated" => ["type" => "datetime"],
            "modified_by" => ["type" => "int","length" => 15,"fk" => ["table" => "user","key" => "user_id","value" => "username"]],
            "name" => ["type" => "varchar","length" => 150],
            "description" => ["type" => "varchar","length" => 500],
            "actual_value" => ["type" => "decimal"],
            "actual_closed_date" => ["type" => "datetime"],
            "discount_amount" => ["type" => "decimal"],
            "estimated_value" => ["type" => "decimal"],
            "estimated_closed_date" => ["type" => "datetime"],
            "budget_amount" => ["type" => "decimal"],
            "confirm_interest" => ["type" => "boolean"],
            "close_probability" => ["type" => "int","length" => 2],
            "need" => ["type" => "boolean"],
            "present_proposal" => ["type" => "boolean"],
            "present_final_proposal" => ["type" => "boolean"],
            "priority_code" => ["type" => "int","length" => 3],
            "stage" => ["type" => "varchar","length" => 15],
            "state_code" => ["type" => "int","length" => 3],
            "status" => ["type" => "varchar","length" => 25],
            "version_number" => ["type" => "int","length" => 3],
            "sys_row_id" => ["type" => "uuid"]
        ];
    }
}