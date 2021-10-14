<?php

namespace Entities\Pages\Models;

use App\Core\AppModel;

class PageModel extends AppModel
{
    protected $EntityName = "Pages";
    protected $ModelName = "Page";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "page_id" => ["type" => "int","length" => 15],
            "company_id" => ["type" => "int","length" => 15],
            "page_parent_id" => ["type" => "int","length" => 15],
            "division_id" => ["type" => "int","length" => 15],
            "unique_url" => ["type" => "varchar","length" => 250],
            "title" => ["type" => "varchar","length" => 150],
            "excerpt" => ["type" => "varchar","length" => 250],
            "uri_request_list" => ["type" => "varchar","length" => 500],
            "columns" => ["type" => "int","length" => 1],
            "created_on" => ["type" => "datetime"],
            "created_by" => ["type" => "int","length" => 15],
            "last_updated" => ["type" => "datetime"],
            "updated_by" => ["type" => "int","length" => 15],
            "meta_title" => ["type" => "varchar","length" => 60],"meta_description" => ["type" => "varchar","length" => 300],"meta_keywords" => ["type" => "varchar","length" => 250],"menu_name" => ["type" => "int","length" => 1],"menu_order" => ["type" => "int","length" => 1],"menu_visibility" => ["type" => "boolean"],"status" => ["type" => "varchar","length" => 15],"locked_for_editing" => ["type" => "boolean"],"ddr_widget" => ["type" => "varchar","length" => 25],"type" => ["type" => "varchar","length" => 10],"page_data" => ["type" => "json","length" => 0]
        ];
    }
}