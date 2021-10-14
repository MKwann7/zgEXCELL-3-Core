<?php

namespace Entities\Products\Models;

use App\Core\AppModel;

class ProductModel extends AppModel
{
    protected $EntityName = "Products";
    protected $ModelName = "Product";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "product_id" => ["type" => "int","length" => 15],
            "product_class_id" => ["type" => "int","length" => 5],
            "product_type_id" => ["type" => "int","length" => 15],
            "product_enduser_id" => ["type" => "int","length" => 15],
            "title" => ["type" => "varchar","length" => 50],
            "abbreviation" => ["type" => "varchar","length" => 75],
            "display_name" => ["type" => "int","length" => 35],
            "description" => ["type" => "int","length" => 250],
            "source_uuid" => ["type" => "uuid"],
            "min_package_value" => ["type" => "decimal"],
            "promo_value" => ["type" => "decimal"],
            "promo_cycle_duration" => ["type" => "int","length" => 5],
            "value" => ["type" => "decimal"],
            "value_duration" => ["type" => "int","length" => 2],
            "cycle_type" => ["type" => "int","length" => 15],
            "status" => ["type" => "varchar","length" => 15],
            "created_on" => ["type" => "datetime"],
            "last_updated" => ["type" => "datetime"],
            "sys_row_id" => ["type" => "uuid"]
        ];
    }
}
