<?php

namespace Entities\Directories\Models;

use App\Core\AppModel;

class DirectoryMemberRelModel extends AppModel
{
    protected string $EntityName = "Directories";
    protected string $ModelName = "DirectoryMemberRel";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions(): array
    {
        return [
            "directory_member_rel_id" => ["type" => "int", "length" => 15],
            "directory_id" => ["type" => "int", "length" => 15],
            "status" => ["type" => "varchar", "length" => 15],
            "user_id" => ["type" => "int", "length" => 15],
            "persona_id" => ["type" => "int", "length" => 15],
            "created_on" => ["type" => "datetime"],
            "last_updated" => ["type" => "datetime"],
            "sys_row_id" => ["type" => "guid"],
        ];
    }
}