<?php

namespace Entities\Cards\Models;

use App\Core\AppModel;

class CardDomainModel extends AppModel
{
    protected string $EntityName = "Cards";
    protected string $ModelName = "CardDomain";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions(): array
    {
        return [
            "card_domain_id" => [ "type" => "int", "length" => 15],
            "company_id" => [ "type" => "int", "length" => 15],
            "card_id" => [ "type" => "int", "length" => 15],
            "domain_name" => [ "type" => "varchar", "length" => 50, "nullable" => true],
            "ssl" => [ "type" => "boolean"],
            "type" => [ "type" => "varchar", "length" => 25],
            "created_on" => [ "type" => "datetime"],
            "last_updated" => [ "type" => "datetime"],
            "sys_row_id" => ["type" => "uuid"]
        ];
    }
}