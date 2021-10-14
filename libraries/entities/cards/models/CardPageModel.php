<?php

namespace Entities\Cards\Models;

use App\Core\AppModel;

class CardPageModel extends AppModel
{
    protected $EntityName = "Cards";
    protected $ModelName = "CardPage";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "card_tab_id" => ["type" => "int","length" => 15],
            "company_id" => ["type" => "int","length" => 15],
            "division_id" => ["type" => "int","length" => 15],
            "user_id" => ["type" => "int","length" => 15],
            "card_page_version" => ["type" => "int","length" => 15],
            "card_tab_type_id" => ["type" => "int","length" => 5,"fk" => ["table" => "card_tab_type","key" => "card_tab_type_id","value" => "name"]],
            "title" => ["type" => "varchar","length" => 100],
            "content" => ["type" => "string"],
            "order_number" => ["type" => "int","length" => 3],
            "url" => ["type" => "varchar","length" => 1000],
            "library_tab" => ["type" => "boolean"],
            "visibility" => ["type" => "boolean"],
            "permanent" => ["type" => "boolean"],
            "instance_count" => ["type" => "int","length" => 5],
            "card_tab_data" => ["type" => "json","length" => 0],
            "created_on" => ["type" => "datetime"],
            "created_by" => ["type" => "int","length" => 15],
            "last_updated" => ["type" => "datetime"],
            "updated_by" => ["type" => "int","length" => 15],
            "old_card_id" => ["type" => "int","length" => 15,"nullable" => true],
            "old_card_tab_id" => ["type" => "int","length" => 15,"nullable" => true],
            "sys_row_id" => ["type" => "char","length" => 36]
        ];
    }
}