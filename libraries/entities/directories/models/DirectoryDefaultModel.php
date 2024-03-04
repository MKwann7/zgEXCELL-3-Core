<?php

namespace Entities\Directories\Models;

use App\Core\AppModel;

class DirectoryDefaultModel extends AppModel
{
    protected string $EntityName = "Directories";
    protected string $ModelName = "DirectoryDefault";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions(): array
    {
        return [
            "directory_default_id" =>["type" => "int", "length" => 15],
            "directory_id" => ["type" => "int", "length" => 15],
            "directory_template_id" => ["type" => "int", "length" => 15],
            "label" => ["type" => "varchar", "length" => 25],
            "value" => ["type" => "varchar", "length" => 1000],
            "created_on" =>["type" => "datetime"],
            "last_updated" =>["type" => "datetime"],
            "sys_row_id" =>["type" => "guid"],
        ];
    }
}