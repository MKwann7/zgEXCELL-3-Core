<?php

namespace Entities\Activities\Models;

use App\Core\AppModel;

class UserLogModel extends AppModel
{
    protected $EntityName = "Activities";
    protected $ModelName = "UserLog";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "log_user_id" => ["type" => "int","length" => 15],
            "user_id" => ["type" => "int","length" => 15],
            "action" => ["type" => "varchar","length" => 15],
            "note" => ["type" => "varchar","length" => 250],
            "entity_name" => ["type" => "varchar","length" => 25],
            "entity_id" => ["type" => "int","length" => 15],
            "created_on" => ["type" => "datetime"],
            "created_by" => ["type" => "int","length" => 15],
            "sys_row_id" => ["type" => "char","length" => 36]
        ];
    }
}