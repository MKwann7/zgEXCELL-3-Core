<?php

namespace Entities\Cards\Models;

use App\Core\AppModel;

class CardSettingModel extends AppModel
{
    protected string $EntityName = "Cards";
    protected string $ModelName = "CardSetting";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions(): array
    {
        return [
            "card_setting_id" => ["type" => "int","length" => 15],
            "card_id" => ["type" => "int","length" => 15],
            "tags" => ["type" => "varchar","length" => 150],
            "label" => ["type" => "varchar","length" => 50],
            "value" => ["type" => "varchar","length" => 1500],
            "options" => ["type" => "varchar","length" => 1500],
            "sys_row_id" => ["type" => "uuid"]
        ];
    }
}