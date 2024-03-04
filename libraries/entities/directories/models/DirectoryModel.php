<?php

namespace Entities\Directories\Models;

use App\Core\AppModel;

class DirectoryModel extends AppModel
{
    protected string $EntityName = "Directories";
    protected string $ModelName = "Directory";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions(): array
    {
        return [
            "directory_id" => [ "type" => "int", "length" => 15],
            "parent_id" => [ "type" => "int", "length" => 15],
            "company_id" => [ "type" => "int", "length" => 15],
            "division_id" => [ "type" => "int", "length" => 15],
            "site_id" => [ "type" => "int", "length" => 15],
            "user_id" => [ "type" => "int", "length" => 15, "fk" => [  "table" => "user",  "key" => "user_id",  "value" => "username" ]],
            "type_id" => [ "type" => "int", "length" => 5], // Standard, Premium, Special, etc.
            "template_id" => [ "type" => "int", "length" => 5],
            "title" => [ "type" => "varchar", "length" => 50],
            "directory_data" => [ "type" => "json", "length" => 0],
            "created_on" => [ "type" => "datetime", "length" => 0],
            "last_updated" => [ "type" => "datetime", "length" => 0],
            "instance_uuid" => [ "type" => "uuid"]
        ];
    }
}