<?php

namespace Entities\Cards\Models;

use App\Core\AppModel;

class CardUserCredentialModel extends AppModel
{
    protected $EntityName = "Cards";
    protected $ModelName = "CardUserCredential";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "card_user_credential_id" => ["type" => "int","length" => 15],
            "company_id" => ["type" => "int","length" => 15],
            "division_id" => ["type" => "int","length" => 15],
            "card_id" => ["type" => "int","length" => 15],
            "user_id" => ["type" => "int","length" => 15],
            "status" => ["type" => "varchar","length" => 35],
            "created_on" => ["type" => "datetime"],
            "last_updated" => ["type" => "datetime"],
            "sys_row_id" => ["type" => "uuid"]
        ];
    }
}