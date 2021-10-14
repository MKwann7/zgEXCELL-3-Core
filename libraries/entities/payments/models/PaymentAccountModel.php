<?php

namespace Entities\Payments\Models;

use App\Core\AppModel;

class PaymentAccountModel extends AppModel
{
    protected $EntityName = "Payments";
    protected $ModelName = "PaymentAccount";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "payment_account_id" => ["type" => "int","length" => 15],
            "company_id" => ["type" => "int","length" => 15],
            "division_id" => ["type" => "int","length" => 15],
            "user_id" => ["type" => "int","length" => 15],
            "payment_type" => ["type" => "int","length" => 5],
            "method" => ["type" => "varchar","length" => 35],
            "type" => ["type" => "varchar","length" => 15],
            "token" => ["type" => "varchar","length" => 255],
            "display_1" => ["type" => "varchar","length" => 25],
            "display_2" => ["type" => "varchar","length" => 25],
            "expiration_date" => ["type" => "datetime"],
            "status" => ["type" => "varchar","length" => 25],
            "created_on" => ["type" => "datetime"],
            "last_updated" => ["type" => "datetime"],
            "sys_row_id" => ["type" => "uuid"]
        ];
    }
}
