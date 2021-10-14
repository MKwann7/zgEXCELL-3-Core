<?php

namespace Entities\Cards\Models;

use App\Core\AppModel;

class CardBrowsingHistoryModel extends AppModel
{
    protected $EntityName = "Cards";
    protected $ModelName = "CardBrowsingHistory";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "card_browsing_history_id" => [ "type" => "int", "length" => 15],
            "company_id" => [ "type" => "int", "length" => 15],
            "user_id" => [ "type" => "int", "length" => 15],
            "card_id" => [ "type" => "int", "length" => 15],
            "note" => [ "type" => "text"],
            "created_on" => [ "type" => "datetime"],
            "last_updated" => [ "type" => "datetime"],
        ];
    }
}