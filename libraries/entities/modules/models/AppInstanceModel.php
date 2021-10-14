<?php

namespace Entities\Modules\Models;

use App\Core\AppModel;

class AppInstanceModel extends AppModel
{
    protected $EntityName = "Modules";
    protected $ModelName = "AppInstance";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "app_instance_id" => ["type" => "int","length" => 15],
            "owner_id" => ["type" => "int","length" => 15],
            "card_id" => ["type" => "int","length" => 15],
            "card_tab_id" => ["type" => "int","length" => 15],
            "card_addon_id" => ["type" => "int","length" => 15],
            "module_app_id" => ["type" => "int","length" => 15],
            "module_app_widget_id" => ["type" => "int","length" => 15],
            "instance_uuid" => ["type" => "uuid"],
            "product_id" => ["type" => "int","length" => 15],
            "created_on" => ["type" => "datetime"],
            "last_updated" => ["type" => "datetime"],
            "sys_row_id" => ["type" => "guid"]
        ];
    }
}