<?php

namespace Entities\Packages\Models;

use App\Core\AppModel;

class PackageLineSettingModel extends AppModel
{
    protected $EntityName = "Packages";
    protected $ModelName = "PackageLine";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "package_line_setting_id" => ["type" => "int","length" => 15],
            "package_line_id" => ["type" => "int","length" => 15],
            "label" => ["type" => "varchar","length" => 25],
            "value" => ["type" => "string"],
            "created_on" => ["type" => "datetime"],
            "last_updated" => ["type" => "datetime"],
            "sys_row_id" => ["type" => "uuid"]
        ];
    }
}