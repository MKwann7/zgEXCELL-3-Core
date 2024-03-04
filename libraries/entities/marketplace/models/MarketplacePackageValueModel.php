<?php

namespace Entities\Marketplace\Models;

use App\Core\AppModel;

class MarketplacePackageValueModel extends AppModel
{
    protected string $EntityName = "Marketplace";
    protected string $ModelName = "MarketplacePackageValue";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions(): array
    {
        return [
            "marketplace_package_value_id" =>["type" => "int", "length" => 15],
            "marketplace_id" =>["type" => "int", "length" => 15],
            "marketplace_package_id" =>["type" => "int", "length" => 15],
            "marketplace_column_id" =>["type" => "int", "length" => 15],
            "type" =>["type" => "varchar", "length" => 15],
            "value" =>["type" => "text"],
            "created_on" =>["type" => "datetime"],
            "last_updated" =>["type" => "datetime"],
            "sys_row_id" => ["type" => "guid"],
        ];
    }
}