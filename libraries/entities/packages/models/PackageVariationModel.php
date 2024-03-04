<?php

namespace App\entities\packages\models;

use App\Core\AppModel;

class PackageVariationModel extends AppModel
{
    protected string $EntityName = "Packages";
    protected string $ModelName = "PackageVariation";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions(): array
    {
        return [
            "package_variation_id" => ["type" => "int","length" => 15],
            "package_id" => ["type" => "int","length" => 15],
            "name" => ["type" => "varchar","length" => 50],
            "description" => ["type" => "varchar","length" => 2000],
            "type" => ["type" => "varchar","length" => 25],
            "promo_price" => ["type" => "decimal","length" => 0],
            "regular_price" => ["type" => "decimal","length" => 0],
            "image" => ["type" => "varchar","length" => 250],
            "order" => ["type" => "int","length" => 5],
            "created_on" => ["type" => "datetime"],
            "last_updated" => ["type" => "datetime"],
            "sys_row_id" => ["type" => "uuid"]
        ];
    }
}

