<?php

namespace Entities\Payments\Classes;

use App\Core\AppEntity;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Payments\Models\PaymentAccountModel;

class PaymentAccounts extends AppEntity
{
    public string $strEntityName       = "payments";
    public $strDatabaseTable    = "payment_account";
    public $strDatabaseName     = "Financial";
    public $strMainModelName    = PaymentAccountModel::class;
    public $strMainModelPrimary = "payment_account_id";
    public $isPrimaryModule     = true;

    public function GetAllActiveProducts() : ExcellTransaction
    {
        return $this->getFks()->getWhere("status","=","Active");
    }

    public static function getToken($id) : ?string
    {
        $paymentAccountResult = (new static())->getById($id);
        if ($paymentAccountResult->result->Count === 0) { return null; }
        return $paymentAccountResult->getData()->first()->token;
    }
}
