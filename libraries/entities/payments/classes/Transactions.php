<?php

namespace Entities\Payments\Classes;

use App\Core\AppEntity;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Payments\Models\TransactionModel;

class Transactions extends AppEntity
{
    public string $strEntityName       = "payments";
    public $strDatabaseTable    = "transaction";
    public $strDatabaseName     = "Financial";
    public $strMainModelName    = TransactionModel::class;
    public $strMainModelPrimary = "transaction_id";

    public function GetAllActiveProducts() : ExcellTransaction
    {
        return $this->getFks()->getWhere("status","=","Active");
    }
}
