<?php

namespace Module\Orders\Models;

use App\Core\AppModel;

class OrderModel extends AppModel
{
    protected $EntityName = "Orders";
    protected $ModelName = "Order";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "order_id" => ["type" => "int","length" => 15],
            "company_id" => ["type" => "int","length" => 15],
            "division_id" => ["type" => "int","length" => 15],
            "user_id" => ["type" => "int","length" => 15,"fk" => ["table" => "user","key" => "user_id","value" => "username"]],
            "total_price" => ["type" => "decimal","length" => 0],
            "opportunity_id" => ["type" => "int","length" => 15,"nullable" => true],
            "quote_id" => ["type" => "int","length" => 15,"nullable" => true],
            "title" => ["type" => "varchar","length" => 75],
            "status" => ["type" => "varchar","length" => 25],
            "created_on" => ["type" => "datetime"],
            "created_by" => ["type" => "int","length" => 15,"fk" => ["table" => "user","key" => "user_id","value" => "username"]],
            "last_updated" => ["type" => "datetime"],
            "updated_by" => ["type" => "int","length" => 15,"fk" => ["table" => "user","key" => "user_id","value" => "username"]],
            "closed_date" => ["type" => "datetime"],
            "closed_by" => ["type" => "int","length" => 15,"fk" => ["table" => "user","key" => "user_id","value" => "username"]],
            "sys_row_id" => ["type" => "char","length" => 36]
        ];
    }
}