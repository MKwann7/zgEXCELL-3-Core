<?php

namespace Entities\Directories\Models;

use App\Core\AppModel;

class DirectorySettingModel extends AppModel
{
    protected string $EntityName = "Directories";
    protected string $ModelName = "DirectorySetting";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions(): array
    {
        return [
            "directory_setting_id" => ["type" => "int","length" => 15],
            "directory_id" => ["type" => "int","length" => 15],
            "label" => ["type" => "varchar","length" => 35],
            "value" => ["type" => "varchar","length" => 7500],
            "sys_row_id" => ["type" => "uuid"]
        ];
    }
}