<?php

namespace Entities\Cart\Models;

use App\Core\AppModel;

class PromoCodeModel extends AppModel
{
    protected $EntityName = "Cart";
    protected $ModelName = "PromoCodes";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "promo_code_id" => ["type" => "int", "length" => 15],
            "company_id" => ["type" => "int", "length" => 15],
            "entity_type" => ["type" => "varchar", "length" => 50],
            "entity_id" => ["type" => "int", "length" => 15],
            "promo_code" => ["type" => "varchar", "length" => 25],
            "title" => ["type" => "varchar", "length" => 50],
            "description" => ["type" => "varchar", "length" => 500],
            "promo_discount_value" => ["type" => "decimal"],
            "discount_value" => ["type" => "decimal"],
            "discount_type" => ["type" => "varchar", "length" => 5],
            "min_entity_value" => ["type" => "decimal"],
            "expiration_date" => ["type" => "datetime"],
            "expired" => ["type" => "boolean"],
            "test_only" => ["type" => "boolean"],
            "created_on" => ["type" => "datetime"],
            "last_updated" => ["type" => "datetime"],
            "sys_row_id" => ["type" => "uuid"]
        ];
    }
}