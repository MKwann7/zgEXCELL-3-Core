<?php

namespace Entities\Cards\Models;

use App\Core\AppModel;

class CardRelModel extends AppModel
{
    protected $EntityName = "Cards";
    protected $ModelName = "CardRel";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "card_rel_id" => ["type" => "int","length" => 15],
            "card_id" => ["type" => "int","length" => 15,"fk" => ["table" => "card","key" => "card_id","value" => "card_name"]],
            "card_rel_group_id" => ["type" => "int","length" => 15],
            "user_id" => ["type" => "int","length" => 15,"fk" => ["table" => "user","key" => "user_id","value" => "username"]],
            "status" => ["type" => "varchar","length" => 15],
            "card_rel_type_id" => ["type" => "int","length" => 15],
            "user_epp_id" => ["type" => "int","length" => 4],
            "mpp_level" => ["type" => "int","length" => 11],
            "created_on" => ["type" => "datetime"],
            "sys_row_id" => ["type" => "char","length" => 36]
        ];
    }
}