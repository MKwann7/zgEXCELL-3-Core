<?php

namespace Entities\Activities\Models;

use App\Core\AppModel;

class AdminLogModel extends AppModel
{
    protected $EntityName = "Activities";
    protected $ModelName = "AdminLog";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "log_admin_id" => ["type" => "int","length" => 15],
            "user_id" => ["type" => "int","length" => 15],
            "action" => ["type" => "varchar","length" => 15],
            "entity_name" => ["type" => "varchar","length" => 25],
            "entity_id" => ["type" => "int","length" => 15],
            "created_on" => ["type" => "datetime"],
            "created_by" => ["type" => "int","length" => 15],
            "sys_row_id" => ["type" => "char","length" => 36]
        ];
    }
}