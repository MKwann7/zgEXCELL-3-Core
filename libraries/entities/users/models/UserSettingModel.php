<?php

namespace Entities\Users\Models;

use App\Core\AppModel;

class UserSettingModel extends AppModel
{
    protected $EntityName = "Users";
    protected $ModelName = "UserSetting";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "user_setting_id" => ["type" => "int","length" => 15],
            "user_id" => ["type" => "int","length" => 15],
            "label" => ["type" => "varchar","length" => 25],
            "value" => ["type" => "varchar","length" => 350],
            "created_on" => ["type" => "datetime"],
            "last_updated" => ["type" => "datetime"],
            "sys_row_id" => ["type" => "uuid"]
        ];
    }
}