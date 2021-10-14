<?php

namespace Entities\Contacts\Models;

use App\Core\AppModel;

class ContactGroupModel extends AppModel
{
    protected $EntityName = "Contacts";
    protected $ModelName = "ContactGroup";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "contact_group_id" =>["type" => "int", "length" => "15"],
            "user_id" =>["type" => "int", "length" => "15"],
            "title" =>["type" => "varchar", "length" => "36"],
            "description" =>["type" => "varchar", "length" => "36"],
            "sys_row_id" =>["type" => "varchar", "length" => "36", "nullable" => true],
        ];
    }
}