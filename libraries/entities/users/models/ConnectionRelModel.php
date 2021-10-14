<?php

namespace Entities\Users\Models;

use App\Core\AppModel;

class ConnectionRelModel extends AppModel
{
    protected $EntityName = "Users";
    protected $ModelName = "ConnectionRel";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return  [
            "connection_rel_id" => ["type" => "int", "length" => 15],
            "connection_id" => ["type" => "int","length" => 15],
            "card_id" => ["type" => "int","length" => 15],
            "card_rel_group_id" => ["type" => "int","length" => 15],
            "status" => ["type" => "varchar","length" => 15],
            "action" => ["type" => "varchar","length" => 15],
            "display_order" => ["type" => "int","length" => 15],
            "sys_row_id" => ["type" => "uuid"]
        ];
    }
}