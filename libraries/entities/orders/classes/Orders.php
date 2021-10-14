<?php

namespace Entities\Orders\Classes;

use App\Core\AppEntity;
use Module\Orders\Models\OrderModel;

class Orders extends AppEntity
{
    public $strEntityName       = "Orders";
    public $strDatabaseTable    = "orders";
    public $strDatabaseName     = "Crm";
    public $strMainModelName    = OrderModel::class;
    public $strMainModelPrimary = "order_id";
    public $isPrimaryModule     = true;

    public function GetAllActiveOrders()
    {
        return $this->getFks()->getWhere("status","=","Active");
    }
}
