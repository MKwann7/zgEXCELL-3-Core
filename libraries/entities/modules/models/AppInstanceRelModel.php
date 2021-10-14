<?php

namespace Entities\Modules\Models;

use App\Core\AppModel;

class AppInstanceRelModel extends AppModel
{
    protected $EntityName = "Modules";
    protected $ModelName = "AppInstanceRel";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "app_instance_rel_id" => ["type" => "int","length" => 15],
            "app_instance_id" => ["type" => "int","length" => 15],
            "company_id" => ["type" => "int","length" => 15],
            "division_id" => ["type" => "int","length" => 15],
            "user_id" => ["type" => "int","length" => 15],
            "card_id" => ["type" => "int","length" => 15],
            "card_page_id" => ["type" => "int","length" => 15],
            "card_page_rel_id" => ["type" => "int","length" => 15],
            "card_addon_id" => ["type" => "int","length" => 15],
            "order_line_id" => ["type" => "int","length" => 15],
            "status" => ["type" => "varchar","length" => 15],
            "created_on" => ["type" => "datetime"],
            "last_updated" => ["type" => "datetime"],
            "sys_row_id" => ["type" => "guid"]
        ];
    }
}