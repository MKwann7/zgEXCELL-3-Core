<?php

namespace Entities\Cards\Models;

use App\Core\AppModel;

class CardAffiliateModel extends AppModel
{
    protected $EntityName = "Cards";
    protected $ModelName = "CardGroup";
    protected $NoDatabase = true;

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "user_id" => [
                "type" => "int",
                "length" => 15
            ],
            "first_name" => [
                "type" => "varchar",
                "length" => 50
            ],
            "last_name" => [
                "type" => "varchar",
                "length" => 50
            ],
            "status" => [
                "type" => "varchar",
                "length" => 15
            ],
            "epp_level" => [
                "type" => "int",
                "length" => 4
            ],
            "epp_value" => [
                "type" => "int",
                "length" => 4
            ]
        ];
    }
}