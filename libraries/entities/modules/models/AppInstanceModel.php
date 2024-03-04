<?php

namespace Entities\Modules\Models;

use App\Core\AppModel;

class AppInstanceModel extends AppModel
{
    protected string $EntityName = "Modules";
    protected string $ModelName = "AppInstance";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions(): array
    {
        return [
            "app_instance_id" => ["type" => "int","length" => 15],
            "owner_id" => ["type" => "int","length" => 15],
            "module_app_id" => ["type" => "int","length" => 15],
            "order_line_id" => ["type" => "int","length" => 15],
            "instance_uuid" => ["type" => "uuid"],
            "product_id" => ["type" => "int","length" => 15],
            "created_on" => ["type" => "datetime"],
            "last_updated" => ["type" => "datetime"],
            "sys_row_id" => ["type" => "guid"]
        ];
    }
}