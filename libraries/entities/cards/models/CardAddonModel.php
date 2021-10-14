<?php

namespace Entities\Cards\Models;

use App\Core\AppModel;

class CardAddonModel extends AppModel
{
    protected $EntityName = "Cards";
    protected $ModelName = "CardAddon";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "card_addon_id" => ["type" => "int","length" => 15],
            "company_id" => ["type" => "int","length" => 15],
            "division_id" => ["type" => "int","length" => 15],
            "user_id" => ["type" => "int","length" => 15,"fk" => ["table" => "user","key" => "user_id","value" => "username"]],
            "card_id" => ["type" => "int","length" => 15,"fk" => ["table" => "card","key" => "card_id","value" => "card_name"]],
            "order_line_id" => ["type" => "int","length" => 15],
            "order_id" => ["type" => "int","length" => 15],
            "product_type_id" => ["type" => "int","length" => 15],
            "product_id" => ["type" => "int","length" => 15],
            "module_id" => ["type" => "int","length" => 15],
            "widget_id" => ["type" => "int","length" => 15],
            "status" => ["type" => "varchar","length" => 15],
            "created_on" => ["type" => "datetime"],
            "last_updated" => ["type" => "datetime"],
            "sys_row_id" => ["type" => "char","length" => 36]
        ];
    }
}