<?php

namespace Entities\Modules\Models;

use App\Core\AppModel;

class ModuleAppEndpointModel extends AppModel
{
    protected $EntityName = "Modules";
    protected $ModelName = "ModuleAppEndpoint";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }
    private function loadDefinitions()
    {
        return [
            "module_app_endpoint_id" =>["type" => "int", "length" => "15"],
            "module_app_id" =>["type" => "int", "length" => "15"],
            "label" =>["type" => "varchar", "length" => "25"],
            "endpoint" =>["type" => "varchar", "length" => "150"],
            "created_at" =>["type" => "datetime"],
            "last_updated" =>["type" => "datetime"],
            "sys_row_id" =>["type" => "guid"],
        ];
    }

    public function ToPublicArray($arProperties = null, $collectionKeys = false)
    {
        $this->RemoveField("module_widget_endpoint_id");
        $this->RemoveField("module_app_id");
        $this->RemoveField("sys_row_id");
        $this->RemoveField("created_at");
        $this->RemoveField("last_updated");

        return $this->ToArray();
    }
}