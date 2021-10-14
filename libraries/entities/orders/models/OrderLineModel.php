<?php

namespace Module\Orders\Models;

use App\Core\AppModel;

class OrderLineModel extends AppModel
{
    protected $EntityName = "Orders";
    protected $ModelName = "OrderLine";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "order_line_id" => ["type" => "int","length" => 15],
            "order_id" => ["type" => "int","length" => 15,"fk" => ["table" => "order","key" => "order_id","value" => "title"]],
            "company_id" => ["type" => "int","length" => 15],
            "product_id" => ["type" => "int","length" => 15,"fk" => ["table" => "product","key" => "product_id","value" => "abbreviation"]],
            "user_id" => ["type" => "int","length" => 15,"fk" => ["table" => "user","key" => "user_id","value" => "username"]],
            "promo_price" => ["type" => "decimal","length" => 0],
            "promo_fee" => ["type" => "decimal","length" => 0],
            "promo_duration" => ["type" => "int","length" => 2],
            "price" => ["type" => "decimal","length" => 0],
            "price_fee" => ["type" => "decimal","length" => 0],
            "price_duration" => ["type" => "int","length" => 2],
            "cycle_type" => ["type" => "int","length" => 2],
            "payment_account_id" => ["type" => "int","length" => 15,"nullable" => true],
            "opportunity_line_id" => ["type" => "int","length" => 15,"nullable" => true],
            "quote_line_id" => ["type" => "int","length" => 15,"nullable" => true],
            "title" => ["type" => "varchar","length" => 75],
            "status" => ["type" => "varchar","length" => 25],
            "billing_date" => ["type" => "datetime"],
            "last_billed" => ["type" => "datetime"],
            "next_bill_date" => ["type" => "datetime"],
            "created_on" => ["type" => "datetime"],
            "created_by" => ["type" => "int","length" => 15,"fk" => ["table" => "user","key" => "user_id","value" => "username"]],
            "last_updated" => ["type" => "datetime"],
            "updated_by" => ["type" => "int","length" => 15,"fk" => ["table" => "user","key" => "user_id","value" => "username"]],
            "closed_date" => ["type" => "datetime"],
            "closed_by" => ["type" => "int","length" => 15,"fk" => ["table" => "user","key" => "user_id","value" => "username"]],
            "data" => ["type" => "json","length" => 0],
            "sys_row_id" => ["type" => "uuid"]
        ];
    }
}