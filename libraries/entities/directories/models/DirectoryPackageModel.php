<?php

namespace Entities\Directories\Models;

use App\Core\AppModel;

class DirectoryPackageModel extends AppModel
{
    protected string $EntityName = "Directories";
    protected string $ModelName = "DirectoryPackage";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions(): array
    {
        return [
            "directory_package_id" => [ "type" => "int", "length" => 15],
            "directory_id" => [ "type" => "int", "length" => 15],
            "package_id" => [ "type" => "int", "length" => 15],
            "permanent_public_viewing" => [ "type" => "bool"],
            "membership" => [ "type" => "bool"],
            "events_discount" => [ "type" => "bool"],
            "events_discount_value" => [ "type" => "decimal"],
            "events_free" => [ "type" => "bool"],
            "status" => [ "type" => "varchar", "length" => 15],
            "created_on" => [ "type" => "datetime", "length" => 0],
            "last_updated" => [ "type" => "datetime", "length" => 0],
            "sys_row_id" => [ "type" => "uuid"]
        ];
    }
}