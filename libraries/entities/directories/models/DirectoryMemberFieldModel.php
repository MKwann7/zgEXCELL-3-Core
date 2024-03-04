<?php

namespace Entities\Directories\Models;

use App\Core\AppModel;

class DirectoryMemberFieldModel extends AppModel
{
    protected string $EntityName = "Directories";
    protected string $ModelName = "DirectoryMemberField";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions(): array
    {
        return [
            "directory_member_field_id" => ["type" => "int", "length" => 15],
            "directory_template_id" => ["type" => "int", "length" => 15],
            "label" => ["type" => "varchar", "length" => 25],
            "name" => ["type" => "varchar", "length" => 25],
            "order" => ["type" => "int", "length" => 5],
            "sortable" => ["type" => "bool"],
            "type" => ["type" => "varchar", "length" => 35],
            "length" => ["type" => "int", "length" => 6],
            "visible" => ["type" => "boolean"],
            "created_on" => ["type" => "datetime"],
            "last_updated" => ["type" => "datetime"],
            "sys_row_id" => ["type" => "guid"],
        ];
    }
}