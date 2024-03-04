<?php

namespace Entities\Orders\Classes;

use App\Core\AppController;
use App\Core\AppEntity;
use Entities\Orders\Models\OrderLineModel;

class OrderLines extends AppEntity
{
    public string $strEntityName       = "Orders";
    public $strDatabaseTable    = "order_line";
    public $strDatabaseName     = "Crm";
    public $strMainModelName    = OrderLineModel::class;
    public $strMainModelPrimary = "order_line_id";

    public function GetAllActiveOrders()
    {
        return $this->getFks()->getWhere("status","=","Active");
    }
}