<?php

namespace Entities\Marketplace\Models;

use App\Core\AppModel;

class MarketplacePackageModel extends AppModel
{
    protected string $EntityName = "Marketplace";
    protected string $ModelName = "MarketplacePackage";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }
    private function loadDefinitions()
    {
        return [
            "marketplace_package_id" => ["type" => "int", "length" => 15],
            "marketplace_id" => ["type" => "int", "length" => 15],
            "status" => ["type" => "varchar", "length" => 15],
            "package_id" => ["type" => "int", "length" => 15],
            "order" => ["type" => "int", "length" => 15],
            "name" => ["type" => "varchar", "length" => 75],
            "description" => ["type" => "varchar", "length" => 5000],
            "promo_price" => ["type" => "decimal","nullable" => true],
            "regular_price" => ["type" => "decimal","nullable" => true],
            "currency" => ["type" => "varchar", "length" => 10],
            "sys_row_id" => ["type" => "guid"],
        ];
    }

}