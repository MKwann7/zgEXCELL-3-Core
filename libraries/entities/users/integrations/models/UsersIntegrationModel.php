<?php

namespace Entities\Users\Integrations\Models;

use App\Core\AppModel;

class UsersIntegrationModel extends AppModel
{
    protected $EntityName = "Users";
    protected $ModelName = "UsersIntegrations";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "integrations_user_id" => ["type" => "int","length" => 15],
            "integration_type" => ["type" => "int","length" => 3],
            "external_id" => ["type" => "varchar","length" => 50],
            "user_id" => ["type" => "int","length" => 15,"fk" => ["table" => "user","key" => "user_id","value" => "username"]],
            "synced" => ["type" => "int","length" => 2],
            "created_on" => ["type" => "date"],
            "last_synced" => ["type" => "date"],
            "state" => ["type" => "varchar","length" => 6]
        ];
    }
}