<?php

namespace Entities\Modules\Models;

use App\Core\AppModel;

class ModuleAppWidgetModel extends AppModel
{
    protected $EntityName = "Modules";
    protected $ModelName = "ModuleAppWidget";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "module_app_widget_id" =>["type" => "int", "length" => "15"],
            "module_app_id" =>["type" => "int", "length" => "15"],
            "widget_api_version" =>["type" => "int", "length" => "5"],
            "widget_class" =>["type" => "int", "length" => "5"],
            "name" =>["type" => "varchar", "length" => "75"],
            "endpoint" =>["type" => "varchar", "length" => "75"],
            "version" =>["type" => "varchar", "length" => "15"],
            "variables" =>["type" => "varchar", "length" => "250"],
            "data" =>["type" => "string"],
            "created_at" =>["type" => "datetime"],
            "last_updated" =>["type" => "datetime"],
            "sys_row_id" =>["type" => "guid"],
        ];
    }

    public function ToPublicArray($arProperties = null, $collectionKeys = false)
    {
        $this->RemoveField("module_widget_component_id");
        $this->RemoveField("module_app_id");
        $this->RemoveField("sys_row_id");
        $this->RemoveField("created_at");
        $this->RemoveField("last_updated");

        return $this->ToArray();
    }
}