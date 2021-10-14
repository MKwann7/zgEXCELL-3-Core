<?php

namespace Entities\Payments\Classes;

use App\Core\AppEntity;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Payments\Models\ArInvoiceModel;

class ArInvoices extends AppEntity
{
    public $strEntityName       = "payments";
    public $strDatabaseTable    = "ar_invoice";
    public $strDatabaseName     = "Financial";
    public $strMainModelName    = ArInvoiceModel::class;
    public $strMainModelPrimary = "ar_invoice_id";

    public function GetAllActiveProducts() : ExcellTransaction
    {
        return $this->getFks()->getWhere("status","=","Active");
    }
}
