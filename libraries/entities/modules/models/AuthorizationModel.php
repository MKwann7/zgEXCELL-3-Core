<?php

namespace Entities\Modules\Models;

use App\Core\AppModel;

class AuthorizationModel extends AppModel
{
    protected $EntityName = "Modules";
    protected $ModelName = "Authorization";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "authorization_id" =>["type" => "int", "length" => "15"],
            "authorization_uuid" =>["type" => "guid"],
            "company_id" =>["type" => "int", "length" => "15"],
            "type" =>["type" => "varchar", "length" => "15"],
            "record_uuid" =>["type" => "guid"],
            "parent_uuid" =>["type" => "guid"],
            "name" =>["type" => "varchar", "length" => "75"],
            "description" =>["type" => "varchar", "length" => "500"],
            "created_at" =>["type" => "datetime"],
            "last_updated" =>["type" => "datetime"],
            "sys_row_id" =>["type" => "guid"],
        ];
    }

    public function ToPublicArray($arProperties = null, $collectionKeys = false)
    {
        $this->AddUnvalidatedValue("id", $this->authorization_uuid, true);
        $this->RemoveField("authorization_id");
        $this->RemoveField("authorization_uuid");
        $this->RemoveField("sys_row_id");
        $this->RemoveField("module_id");
        $this->RemoveField("company_id");

        return $this->ToArray();
    }
}