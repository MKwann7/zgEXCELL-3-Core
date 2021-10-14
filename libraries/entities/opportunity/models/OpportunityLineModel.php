<?php

namespace Entities\Opportunity\Models;

use App\Core\AppModel;

class OpportunityLineModel extends AppModel
{
    protected $EntityName = "Opportunity";
    protected $ModelName = "OpportunityLine";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "opportunity_line_id" => ["type" => "int","length" => 15],
            "opportunity_id" => ["type" => "int","length" => 15],
            "company_id" => ["type" => "int","length" => 15],
            "division_id" => ["type" => "int","length" => 15],
            "user_id" => ["type" => "int","length" => 15,"fk" => ["table" => "user","key" => "user_id","value" => "username"]],
            "owner_id" => ["type" => "int","length" => 15,"fk" => ["table" => "user","key" => "user_id","value" => "username"]],
            "product_plan_id" => ["type" => "int","length" => 15],
            "name" => ["type" => "varchar","length" => 50],
            "description" => ["type" => "varchar","length" => 500],
            "price_per_unit" => ["type" => "decimal"],
            "quantity" => ["type" => "int","length" => 2],
            "sys_row_id" => ["type" => "uuid"]
        ];
    }
}