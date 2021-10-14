<?php

namespace Entities\Users\Models;

use App\Core\AppModel;

class UserClassModel extends AppModel
{
    protected $EntityName = "Users";
    protected $ModelName = "UserClass";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "user_class_id" => ["type" => "int","length" => 15],
            "user_id" => ["type" => "int","length" => 15,"fk" => ["table" => "user","key" => "user_id","value" => "username"]],
            "user_class_type_id" => ["type" => "int","length" => 15,"fk" => ["table" => "user_class_type","key" => "user_class_type_id","value" => "name"]],
            "user_class_product_id" => ["type" => "int","length" => 5],
            "order_line_id" => ["type" => "int","length" => 15],
            "epp_id" => ["type" => "int","length" => 4],
            "epp_graceperiod" => ["type" => "int","length" => 4],
            "epp_start" => ["type" => "datetime"],
            "sys_row_id" => ["type" => "char","length" => 36,"nullable" => true]
        ];
    }
}