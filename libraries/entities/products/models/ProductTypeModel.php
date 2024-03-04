<?php

namespace Entities\Products\Models;

use App\Core\AppModel;

class ProductTypeModel extends AppModel
{
    protected string $EntityName = "Products";
    protected string $ModelName = "ProductType";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions(): array
    {
        return [
            "product_type_id" => ["type" => "int","length" => 15],
            "product_primary" => ["type" => "boolean"],
            "abbreviation" => ["type" => "varchar","length" => 15],
            "name" => ["type" => "int","length" => 50],
            "description" => ["type" => "int","length" => 250],
            "created_on" => ["type" => "datetime"],
            "last_updated" => ["type" => "datetime"],
            "sys_row_id" => ["type" => "uuid"]
        ];
    }
}
