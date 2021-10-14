<?php

namespace Entities\Modules\Models;

use App\Core\AppModel;

class ModuleMainModel extends AppModel
{
    protected $EntityName = "Modules";
    protected $ModelName = "ModuleMain";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "module_id" =>["type" => "int", "length" => "15"],
            "company_id" =>["type" => "int", "length" => "15"],
            "name" =>["type" => "varchar", "length" => "75"],
            "module_uuid" =>["type" => "guid"],
            "author" =>["type" => "varchar", "length" => "50"],
            "version" =>["type" => "varchar", "length" => "15"],
            "category" =>["type" => "varchar", "length" => "25"],
            "tags" =>["type" => "string"],
            "created_at" =>["type" => "datetime"],
            "last_updated" =>["type" => "datetime"],
            "sys_row_id" =>["type" => "guid"],
        ];
    }

    public function ToPublicArray($arProperties = null, $collectionKeys = false)
    {
        $this->AddUnvalidatedValue("id", $this->module_uuid, true);
        $this->RemoveField("module_uuid");
        $this->RemoveField("sys_row_id");
        $this->RemoveField("module_id");
        $this->RemoveField("company_id");

        return $this->ToArray();
    }
}