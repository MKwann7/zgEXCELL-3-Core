<?php

namespace Entities\Users\Models;

use App\Core\AppModel;

class UserAddressModel extends AppModel
{
    protected $EntityName = "Users";
    protected $ModelName = "UserAddress";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
             "address_id" => ["type" => "int","length" => 15],
             "user_id" => ["type" => "int","length" => 15,"fk" => ["table" => "user","key" => "user_id","value" => "username"]],
             "display_name" => ["type" => "varchar","length" => 35],
             "address_1" => ["type" => "varchar","length" => 50],
             "address_2" => ["type" => "varchar","length" => 35,"nullable" => true],
             "address_3" => ["type" => "varchar","length" => 25],
             "city" => ["type" => "varchar","length" => 50],
             "state" => ["type" => "varchar","length" => 25],
             "zip" => ["type" => "int","length" => 5],
             "country" => ["type" => "varchar","length" => 35],
             "phone_number" => ["type" => "varchar","length" => 20,"nullable" => true],
             "fax_number" => ["type" => "varchar","length" => 20,"nullable" => true],
             "is_primary" => ["type" => "boolean","nullable" => true],
             "sys_row_id" => ["type" => "char","length" => 36,"nullable" => true]
         ];
    }
}