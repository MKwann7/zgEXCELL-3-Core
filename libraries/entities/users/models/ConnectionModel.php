<?php

namespace Entities\Users\Models;

use App\Core\AppModel;

class ConnectionModel extends AppModel
{
    protected $EntityName = "Users";
    protected $ModelName = "Connection";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return  [
            "connection_id" => ["type" => "int","length" => 15],
            "division_id" => ["type" => "int","length" => 15,"fk" => ["table" => "division","key" => "division_id","value" => "division_name"]],
            "company_id" => ["type" => "int","length" => 15,"fk" => ["table" => "company","key" => "company_id","value" => "company_name"]],
            "user_id" => ["type" => "int","length" => 15,"fk" => ["table" => "user","key" => "user_id","value" => "username"]],
            "connection_type_id" => ["type" => "int","length" => 15,"fk" => ["table" => "connection_type","key" => "connection_type_id","value" => "name"]],
            "connection_value" => ["type" => "varchar","length" => 150],
            "is_primary" => ["type" => "boolean"],
            "connection_class" => ["type" => "varchar","length" => 20],
            "sys_row_id" => ["type" => "char","length" => 36,"nullable" => true]
        ];
    }
}