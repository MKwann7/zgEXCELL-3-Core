<?php

namespace Entities\Directories\Models;

use App\Core\AppModel;

class DirectoryTemplateModel extends AppModel
{
    protected string $EntityName = "Directories";
    protected string $ModelName = "DirectoryTemplate";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions(): array
    {
        return [
            "directory_template_id" => ["type" => "int", "length" => 15],
            "template_name" => ["type" => "varchar", "length" => 50],
            "created_on" => ["type" => "datetime"],
            "last_updated" => ["type" => "datetime"],
            "sys_row_id" => ["type" => "guid"],
        ];
    }
}