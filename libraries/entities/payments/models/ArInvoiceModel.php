<?php

namespace Entities\Payments\Models;

use App\Core\AppModel;

class ArInvoiceModel extends AppModel
{
    protected $EntityName = "Payments";
    protected $ModelName = "ArInvoice";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "ar_invoice_id" => ["type" => "int","length" => 15],
            "company_id" => ["type" => "int","length" => 15],
            "division_id" => ["type" => "int","length" => 15],
            "user_id" => ["type" => "int","length" => 15],
            "gross_value" => ["type" => "decimal"],
            "net_value" => ["type" => "decimal"],
            "tax" => ["type" => "decimal"],
            "payment_account_id" => ["type" => "int","length" => 15],
            "payment_type_id" => ["type" => "int","length" => 15],
            "status" => ["type" => "varchar","length" => 25],
            "created_on" => ["type" => "datetime"],
            "created_by" => ["type" => "int","length" => 15],
            "last_updated" => ["type" => "datetime"],
            "updated_by" => ["type" => "int","length" => 15],
            "closed_date" => ["type" => "datetime"],
            "closed_by" => ["type" => "int","length" => 15],
            "sys_row_id" => ["type" => "uuid"]
        ];
    }
}
