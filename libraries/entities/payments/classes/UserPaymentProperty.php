<?php

namespace Entities\Payments\Classes;

use App\Core\AppEntity;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Payments\Models\UserPaymentPropertyModel;

class UserPaymentProperty extends AppEntity
{
    public $strEntityName       = "payments";
    public $strDatabaseTable    = "user_payment_property";
    public $strDatabaseName     = "Financial";
    public $strMainModelName    = UserPaymentPropertyModel::class;
    public $strMainModelPrimary = "user_payment_property_id";

    public function GetAllActiveProducts() : ExcellTransaction
    {
        return $this->getFks()->getWhere("status","=","Active");
    }
}
