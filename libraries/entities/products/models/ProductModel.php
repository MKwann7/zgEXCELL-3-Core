<?php

namespace Entities\Products\Models;

use App\Core\AppModel;

/**
 * @property int $product_id
 * @property int $product_class_id
 * @property int $product_type_id
 * @property int $product_enduser_id
 * @property string $title
 * @property string $abbreviation
 * @property string $display_name
 * @property string $description
 * @property string $source_uuid
 * @property float $min_package_value
 * @property float $promo_value
 * @property int $promo_cycle_duration
 * @property float $value
 * @property int $value_duration
 * @property int $cycle_type
 * @property string $status
 * @property string $created_on
 * @property string $last_updated
 * @property string $sys_row_id
 */

class ProductModel extends AppModel
{
    protected string $EntityName = "Products";
    protected string $ModelName = "Product";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions(): array
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
