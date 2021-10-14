<?php

namespace Entities\Packages\Models;

use App\Core\AppModel;

class PackageLineModel extends AppModel
{
    protected $EntityName = "Packages";
    protected $ModelName = "PackageLine";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "package_line_id" => ["type" => "int","length" => 15],
            "package_id" => ["type" => "int","length" => 15],
            "company_id" => ["type" => "int","length" => 15],
            "division_id" => ["type" => "int","length" => 15],
            "product_entity" => ["type" => "varchar","length" => 35],
            "product_entity_id" => ["type" => "int","length" => 15],
            "journey_id" => ["type" => "int","length" => 15],
            "name" => ["type" => "varchar","length" => 75],
            "description" => ["type" => "varchar","length" => 500],
            "quantity" => ["type" => "int","length" => 5],
            "regular_price" => ["type" => "decimal","length" => 0],
            "promo_price" => ["type" => "decimal","length" => 0],
            "recurring_price" => ["type" => "decimal","length" => 0],
            "product_price_override" => ["type" => "decimal","length" => 0],
            "product_promo_price_override" => ["type" => "decimal","length" => 0],
            "currency" => ["type" => "varchar","length" => 10],
            "order" => ["type" => "int","length" => 5],
            "created_on" => ["type" => "datetime"],
            "last_updated" => ["type" => "datetime"],
            "sys_row_id" => ["type" => "uuid"]
        ];
    }
}