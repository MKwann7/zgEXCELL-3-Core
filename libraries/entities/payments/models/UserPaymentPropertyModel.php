<?php

namespace Entities\Payments\Models;

use App\Core\AppModel;

class UserPaymentPropertyModel extends AppModel
{
    protected $EntityName = "Payments";
    protected $ModelName = "UserPaymentProperty";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "user_payment_property_id" => ["type" => "int","length" => 15],
            "company_id" => ["type" => "int","length" => 15],
            "user_id" => ["type" => "int","length" => 15],
            "type_id" => ["type" => "int","length" => 5],
            "state" => ["type" => "varchar","length" => 5],
            "value" => ["type" => "varchar","length" => 255],
            "sys_row_id" => ["type" => "uuid"]
        ];
    }
}
