<?php

namespace Entities\Modules\Models;

use App\Core\AppModel;

class ModuleAppModel extends AppModel
{
    protected $EntityName = "Modules";
    protected $ModelName = "ModuleApp";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "module_app_id" =>["type" => "int", "length" => "15"],
            "module_id" =>["type" => "int", "length" => "15"],
            "company_id" =>["type" => "int", "length" => "15"],
            "app_uuid" =>["type" => "guid"],
            "name" =>["type" => "varchar", "length" => "75"],
            "author" =>["type" => "varchar", "length" => "50"],
            "domain" =>["type" => "varchar", "length" => "150"],
            "version" =>["type" => "varchar", "length" => "15"],
            "ui_type" =>["type" => "varchar", "length" => "50"],
            "category" =>["type" => "varchar", "length" => "25"],
            "tags" =>["type" => "string"],
            "created_on" =>["type" => "datetime"],
            "last_updated" =>["type" => "datetime"],
            "sys_row_id" =>["type" => "guid"],
        ];
    }

    public function ToPublicArray($arProperties = null, $collectionKeys = false)
    {
        $this->AddUnvalidatedValue("id", $this->app_uuid, true);
        $this->RemoveField("module_app_id");
        $this->RemoveField("module_id");
        $this->RemoveField("app_uuid");
        $this->RemoveField("sys_row_id");
        $this->RemoveField("company_id");

        return $this->ToArray();
    }
}