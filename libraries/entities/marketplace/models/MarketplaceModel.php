<?php

namespace Entities\Marketplace\Models;

use App\Core\AppModel;

class MarketplaceModel extends AppModel
{
    protected string $EntityName = "Marketplace";
    protected string $ModelName = "Marketplace";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions(): array
    {
        return [
            "marketplace_id" =>["type" => "int", "length" => "15"],
            "instance_uuid" => ["type" => "guid"],
            "template_id" => ["type" => "int", "length" => "15"],
            "created_on" =>["type" => "datetime"],
            "last_updated" =>["type" => "datetime"],
        ];
    }
}