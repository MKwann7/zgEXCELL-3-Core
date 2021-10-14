<?php

namespace Entities\Cards\Models;

use App\Core\AppModel;

class CardPageRelModel extends AppModel
{
    protected $EntityName = "Cards";
    protected $ModelName = "CardPageRel";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "card_tab_rel_id" => ["type" => "int","length" => 15],
            "card_tab_id" => ["type" => "int","length" => 15],
            "card_id" => ["type" => "int","length" => 15,"fk" => ["table" => "card","key" => "card_id","value" => "card_name"]],
            "user_id" => ["type" => "int","length" => 15,"fk" => ["table" => "user","key" => "user_id","value" => "username"]],
            "card_addon_id" => ["type" => "int","length" => 15],
            "order_line_id" => ["type" => "int","length" => 15],
            "rel_sort_order" => ["type" => "int","length" => 5],
            "rel_visibility" => ["type" => "boolean"],
            "card_tab_rel_data" => ["type" => "json","length" => 0],
            "card_tab_rel_type" => ["type" => "varchar","length" => 15],
            "synced_state" => ["type" => "int","length" => 2],
            "sys_row_id" => ["type" => "char","length" => 36]
        ];
    }
}