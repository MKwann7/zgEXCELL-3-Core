<?php

namespace Entities\Packages\Models;

use App\Core\AppModel;

class PackageModel extends AppModel
{
    protected $EntityName = "Packages";
    protected $ModelName = "PackageLineSetting";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "package_id" => ["type" => "int","length" => 15],
            "company_id" => ["type" => "int","length" => 15],
            "division_id" => ["type" => "int","length" => 15],
            "name" => ["type" => "varchar","length" => 75],
            "description" => ["type" => "varchar","length" => 500],
            "enduser_id" => ["type" => "varchar","length" => 500],
            "promo_price" => ["type" => "decimal","length" => 0],
            "regular_price" => ["type" => "decimal","length" => 0],
            "currency" => ["type" => "varchar","length" => 10],
            "order" => ["type" => "int","length" => 5],
            "max_quantity" => ["type" => "int","length" => 5],
            "image_url" => ["type" => "varchar","length" => 150],
            "created_on" => ["type" => "datetime"],
            "last_updated" => ["type" => "datetime"],
            "sys_row_id" => ["type" => "uuid"]
        ];
    }
}
