<?php

namespace Entities\Cards\Models;

use App\Core\AppModel;

class CardPageAppPropertyModel extends AppModel
{
    protected $EntityName = "Cards";
    protected $ModelName = "CardPageWidgetProperty";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "card_tab_app_property_id" => ["type" => "int","length" => 15],
            "card_tab_app_id" => ["type" => "int","length" => 15],
            "module_widget_type_id" => ["type" => "int","length" => 15],
            "module_widget_source" => ["type" => "varchar","length" => 25],
            "value" => ["type" => "varchar","length" => 500],
            "created_on" => ["type" => "datetime"],
            "last_updated" => ["type" => "datetime"],
            "sys_row_id" => ["type" => "guid"]
        ];
    }
}