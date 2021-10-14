<?php

namespace Entities\Cards\Models;

use App\Core\AppModel;

class CardSocialMediaModel extends AppModel
{
    protected $EntityName = "Cards";
    protected $ModelName = "CardSocialMedia";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "card_socialmedia_id" => ["type" => "int","length" => 15],
            "company_id" => ["type" => "int","length" => 15],
            "division_id" => ["type" => "int","length" => 15],
            "user_id" => ["type" => "int","length" => 15,"fk" => ["table" => "user","key" => "user_id","value" => "username"]],
            "card_id" => ["type" => "int","length" => 15,"fk" => ["table" => "card","key" => "card_id","value" => "card_name"]],
            "connection_id" => ["type" => "int","length" => 15],
            "action" => ["type" => "varchar","length" => 15],
            "display_order" => ["type" => "int","length" => 5],
            "status" => ["type" => "varchar","length" => 15],
            "created_on" => ["type" => "datetime"],
            "last_updated" => ["type" => "datetime"],
            "sys_row_id" => ["type" => "uuid"]
        ];
    }
}