<?php

namespace Entities\Packages\Models;

use App\Core\AppModel;

class PackageModel extends AppModel
{
    protected string $EntityName = "Packages";
    protected string $ModelName = "PackageLineSetting";

    public const TYPE_DEFAULT = "defualt";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions(): array
    {
        return [
            "package_id" => ["type" => "int","length" => 15],
            "type" => ["type" => "varchar","length" => 25],
            "source" => ["type" => "varchar","length" => 25],
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
            "hide_line_items" => ["type" => "int","length" => 5],
            "image_url" => ["type" => "varchar","length" => 150],
            "created_on" => ["type" => "datetime"],
            "last_updated" => ["type" => "datetime"],
            "sys_row_id" => ["type" => "uuid"]
        ];
    }
}
