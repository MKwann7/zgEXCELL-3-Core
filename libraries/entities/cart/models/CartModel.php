<?php

namespace Entities\Cart\Models;

use App\Core\AppModel;

class CartModel extends AppModel
{
    protected $EntityName = "Cart";
    protected $ModelName = "Cart";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "cart_id" => ["type" => "int", "length" => 15],
            "company_id" => ["type" => "int", "length" => 15],
            "division_id" => ["type" => "int", "length" => 15],
            "user_id" => ["type" => "int","length" => 15],
            "package_data" => ["type" => "string"],
            "created_on" => ["type" => "datetime"],
            "last_updated" => ["type" => "datetime"],
            "sys_row_id" => ["type" => "uuid"]
        ];
    }
}