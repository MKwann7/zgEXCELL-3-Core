<?php

namespace Entities\Payments\Models;

use App\Core\AppModel;

class TransactionModel extends AppModel
{
    protected $EntityName = "Payments";
    protected $ModelName = "Transaction";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "transaction_id" => ["type" => "int","length" => 15],
            "company_id" => ["type" => "int","length" => 15],
            "division_id" => ["type" => "int","length" => 15],
            "user_id" => ["type" => "int","length" => 15],
            "ar_invoice_id" => ["type" => "int","length" => 15],
            "package_id" => ["type" => "int","length" => 15],
            "package_line_id" => ["type" => "int","length" => 15],
            "product_entity" => ["type" => "varchar","length" => 35],
            "product_entity_id" => ["type" => "int","length" => 15],
            "order_id" => ["type" => "int","length" => 15],
            "order_line_id" => ["type" => "int","length" => 15],
            "gross_value" => ["type" => "decimal"],
            "net_value" => ["type" => "decimal"],
            "tax" => ["type" => "decimal"],
            "transaction_type_id" => ["type" => "int","length" => 5],
            "created_on" => ["type" => "datetime"],
            "sys_row_id" => ["type" => "uuid"]
        ];
    }
}
