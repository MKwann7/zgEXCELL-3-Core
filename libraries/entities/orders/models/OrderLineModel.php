<?php

namespace Entities\Orders\Models;

use App\Core\AppModel;

/**
 * @property int $order_line_id
 * @property int $order_id
 * @property int $company_id
 * @property int $product_id
 * @property int $user_id
 * @property float $promo_price
 * @property float $promo_fee
 * @property int $promo_duration
 * @property float $price
 * @property float $price_fee
 * @property int $price_duration
 * @property int $cycle_type
 * @property int $payment_account_id
 * @property int $opportunity_line_id
 * @property int $quote_line_id
 * @property string $title
 * @property string $status
 * @property string $billing_date
 * @property string $last_billed
 * @property string $next_bill_date
 * @property string $created_on
 * @property int $created_by
 * @property string $last_updated
 * @property int $updated_by
 * @property string $closed_date
 * @property int $closed_by
 * @property string $data
 * @property string $sys_row_id
 */

class OrderLineModel extends AppModel
{
    protected string $EntityName = "Orders";
    protected string $ModelName = "OrderLine";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions(): array
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