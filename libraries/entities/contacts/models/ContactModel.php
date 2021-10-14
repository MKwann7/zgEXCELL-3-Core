<?php

namespace Entities\Contacts\Models;

use App\Core\AppModel;

class ContactModel extends AppModel
{
    protected $EntityName = "Contacts";
    protected $ModelName = "Contact";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "contact_id" =>["type" => "int", "length" => "15"],
            "company_id" =>["type" => "int", "length" => "15"],
            "division_id" =>["type" => "int", "length" => "15"],
            "user_id" =>["type" => "int", "length" => "15"],
            "first_name" =>["type" => "varchar", "length" => "50"],
            "last_name" =>["type" => "varchar", "length" => "50"],
            "phone" =>["type" => "varchar", "length" => "10"],
            "email" =>["type" => "varchar", "length" => "150"],
            "birth_date" =>["type" => "datetime"],
            "mobiniti_id" =>["type" => "varchar", "length" => "36"],
            "created_on" =>["type" => "datetime"],
            "last_updated" =>["type" => "datetime"],
            "sys_row_id" =>["type" => "varchar", "length" => "36", "nullable" => true],
        ];
    }
}