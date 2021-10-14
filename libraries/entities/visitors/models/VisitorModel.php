<?php

namespace Entities\Visitors\Models;

use App\Core\AppModel;

class VisitorModel extends AppModel
{
    protected $EntityName = "Visitors";
    protected $ModelName = "Visitor";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }
    private function loadDefinitions()
    {
        return [
            "visitor_activity_id" => ["type" => "int","length" => 15],
            "visitor_activity_guid" => ["type" => "char","length" => 36],
            "company_id" => ["type" => "int","length" => 15,"nullable" => true,"fk" => ["table" => "company","key" => "company_id","value" => "company_name"]],
            "division_id" => ["type" => "int","length" => 15,"nullable" => true,"fk" => ["table" => "division","key" => "division_id","value" => "division_name"]],
            "user_id" => ["type" => "int","length" => 15,"nullable" => true,"fk" => ["table" => "user","key" => "user_id","value" => "username"]],
            "card_id" => ["type" => "int","length" => 15,"nullable" => true,"fk" => ["table" => "card","key" => "card_id","value" => "card_name"]],
            "activity_type" => ["type" => "varchar","length" => 25],
            "created_on" => ["type" => "datetime"],
            "ip_address" => ["type" => "varchar","length" => 25],
            "address_city" => ["type" => "varchar","length" => 35],
            "address_state" => ["type" => "varchar","length" => 25],
            "address_zip" => ["type" => "int","length" => 15],
            "address_country" => ["type" => "varchar","length" => 25],
            "address_loc" => ["type" => "varchar","length" => 50],
            "visitor_data" => ["type" => "text"]
        ];
    }
}