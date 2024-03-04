<?php

namespace Entities\Users\Models;

use App\Core\AppModel;

class ConnectionTypeModel extends AppModel
{
    protected string $EntityName = "Users";
    protected string $ModelName = "Connection";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions(): array
    {
        return  [
            "connection_type_id" => ["type" => "int","length" => 15],
            "abbreviation" => ["type" => "varchar","length" => 25],
            "name" => ["type" => "varchar","length" => 50],
            "action" => ["type" => "varchar","length" => 15],
            "font_awesome" => ["type" => "varchar","length" => 35],
            "sys_row_id" => ["type" => "uuid"]
        ];
    }
}