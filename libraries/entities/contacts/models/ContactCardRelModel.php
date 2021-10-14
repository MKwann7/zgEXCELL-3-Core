<?php

namespace Entities\Contacts\Models;

use App\Core\AppModel;

class ContactCardRelModel extends AppModel
{
    protected $EntityName = "Contacts";
    protected $ModelName = "ContactCardRel";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "contact_card_rel_id" =>["type" => "int", "length" => 15],
            "contact_id" =>["type" => "int", "length" => 15],
            "card_id" =>["type" => "int", "length" => 15],
            "mobiniti_contact_id" =>["type" => "varchar", "length" => 36, "nullable" => true],
            "mobiniti_group_id" =>["type" => "varchar", "length" => 36, "nullable" => true],
        ];
    }
}