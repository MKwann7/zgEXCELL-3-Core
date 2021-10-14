<?php

namespace Entities\Cards\Models;

use App\Core\AppModel;

class CardConnectionModel extends AppModel
{
    protected $EntityName = "Cards";
    protected $ModelName = "CardConnection";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "connection_rel_id" => ["type" => "int","length" => 15],
            "connection_id" => ["type" => "int","length" => 15,"fk" => ["table" => "connection","key" => "connection_id","value" => "connection_value"]],
            "card_id" => ["type" => "int","length" => 15,"fk" => ["table" => "card","key" => "card_id","value" => "card_name"]],
            "card_rel_group_id" => ["type" => "int","length" => 15,"fk" => ["table" => "card_rel_group","key" => "card_rel_group_id","value" => "name"]],
            "status" => ["type" => "varchar","length" => 15],
            "action" => ["type" => "varchar","length" => 15],
            "display_order" => ["type" => "int","length" => 15,"nullable" => true],
            "sys_row_id" => ["type" => "char","length" => 36]
        ];
    }
}