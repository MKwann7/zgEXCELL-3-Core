<?php

namespace Entities\Contacts\Models;

use App\Core\AppModel;

class ContactUserRelModel extends AppModel
{
    protected $EntityName = "Contacts";
    protected $ModelName = "ContactUserRel";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "contact_user_rel_id" =>["type" => "int", "length" => "15"],
            "contact_id" =>["type" => "int", "length" => "15"],
            "user_id" =>["type" => "int", "length" => "15"],
            "mobiniti_contact_id" =>["type" => "varchar", "length" => "36", "nullable" => true],
        ];
    }
}