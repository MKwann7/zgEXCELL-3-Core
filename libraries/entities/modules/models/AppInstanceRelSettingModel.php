<?php

namespace Entities\Modules\Models;

use App\Core\AppModel;

class AppInstanceRelSettingModel extends AppModel
{
    protected string $EntityName = "Modules";
    protected string $ModelName = "AppInstanceRelSetting";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions(): array
    {
        return [
            "app_instance_rel_setting_id" => ["type" => "int","length" => 15],
            "app_instance_rel_id" => ["type" => "int","length" => 15],
            "label" => ["type" => "varchar","length" => 35],
            "value" => ["type" => "varchar","length" => 7500],
            "sys_row_id" => ["type" => "uuid"]
        ];
    }
}