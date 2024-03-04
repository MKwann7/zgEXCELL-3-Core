<?php

namespace Entities\Marketplace\Models;

use App\Core\AppModel;

class MarketPlaceColumnModel extends AppModel
{
    protected string $EntityName = "Marketplace";
    protected string $ModelName = "MarketPlaceColumn";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions(): array
    {
        return [
            "marketplace_column_id" => ["type" => "int", "length" => 15],
            "template_id" => ["type" => "int", "length" => 15],
            "label" => ["type" => "varchar", "length" => 25],
            "name" => ["type" => "varchar", "length" => 25],
            "order" => ["type" => "int", "length" => 5],
            "type" => ["type" => "varchar", "length" => 35],
            "length" => ["type" => "int", "length" => 6],
            "visible" => ["type" => "boolean"],
            "created_on" => ["type" => "datetime"],
            "last_updated" => ["type" => "datetime"],
            "sys_row_id" => ["type" => "guid"],
        ];
    }
}