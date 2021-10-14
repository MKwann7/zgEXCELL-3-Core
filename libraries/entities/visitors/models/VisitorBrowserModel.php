<?php

namespace Entities\Visitors\Models;

use App\Core\AppModel;

class VisitorBrowserModel extends AppModel
{
    protected $EntityName = "Visitors";
    protected $ModelName = "VisitorBrowser";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "visitor_browser_id" => ["type" => "int","length" => 15],
            "user_id" => ["type" => "int","length" => 15,"nullable" => true,"fk" => ["table" => "user","key" => "user_id"]],
            "contact_id" => ["type" => "int","length" => 15,"nullable" => true],
            "browser_cookie" => ["type" => "varchar","length" => 150],
            "browser_ip" => ["type" => "varchar","length" => 50],
            "logged_in_at" => ["type" => "datetime","nullable" => true],
            "created_on" => ["type" => "datetime"],
            "browser_type" => ["type" => "varchar","length" => 25],
            "device_type" => ["type" => "varchar","length" => 25]
        ];
    }
}