<?php

namespace Entities\Cards\Models;

use App\Core\AppModel;

class CardGroupModel extends AppModel
{
    protected $EntityName = "Cards";
    protected $ModelName = "CardGroup";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "card_rel_group_id" => [ "type" => "int", "length" => 15],
            "user_id" => [ "type" => "int", "length" => 15, "fk" => [  "table" => "user",  "key" => "user_id",  "value" => "username" ]],
            "card_id" => [ "type" => "int", "length" => 15],
            "name" => [ "type" => "varchar", "length" => 50],
            "description" => [ "type" => "varchar", "length" => 250],
            "status" => [ "type" => "varchar", "length" => 25],
            "card_rel_group_parent_id" => [ "type" => "int", "length" => 15, "nullable" => true],
            "created_on" => [ "type" => "datetime"],
            "created_by" => [ "type" => "int", "length" => 15],
            "last_updated" => [ "type" => "datetime"],
            "updated_by" => [ "type" => "int", "length" => 15],
            "sys_row_id" => [ "type" => "char", "length" => 36]
        ];
    }
}