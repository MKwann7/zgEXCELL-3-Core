<?php

namespace Entities\Companies\Models;

use App\Core\AppModel;

class CompanySettingModel extends AppModel
{
    protected $EntityName = "Packages";
    protected $ModelName = "CompanySetting";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "company_setting_id" => ["type" => "int","length" => 15],
            "company_id" => ["type" => "int","length" => 15],
            "label" => ["type" => "varchar","length" => 35],
            "value" => ["type" => "varchar","length" => 350],
            "created_on" => ["type" => "datetime"],
            "last_updated" => ["type" => "datetime"],
            "sys_row_id" => ["type" => "uuid"]
        ];
    }
}