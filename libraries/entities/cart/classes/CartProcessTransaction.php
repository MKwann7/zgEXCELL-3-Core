<?php

namespace Entities\Cart\Classes;

use App\Utilities\Excell\ExcellCollection;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Cart\Models\PromoCodeModel;
use Module\Orders\Models\OrderModel;

class CartProcessTransaction
{
    public $totalCartValue;
    public $totalProductsValue;
    /** @var $promoCode PromoCodeModel   */
    public $promoCode = null;
    /** @var $transaction ExcellTransaction */
    public $transaction;
    /** @var $errors ExcellCollection */
    public $errors;
    /** @var $packages ExcellCollection */
    public $packages;
    /**@var $order OrderModel */
    public $order = null;
    public $arInvoice = null;
    public $userId = null;
    public $companyId = null;
    public $defaultUserId = null;

    public function __construct ()
    {
        $this->transaction = new ExcellTransaction();
    }

    public function getTransaction(): ExcellTransaction
    {
        return $this->transaction;
    }

    public function setErrors(ExcellCollection $errors): self
    {
        if ($errors->Count() > 0)
        {
            $this->transaction->Result->Success = false;
        }

        $this->errors = $errors;
        return $this;
    }

    public function getErrors(): ExcellCollection
    {
        return $this->errors;
    }

    public function setPackages(ExcellCollection $packages): self
    {
        $this->packages = $packages;
        return $this;
    }

    public function getPackages(): ExcellCollection
    {
        return $this->packages;
    }

    public function setSuccessTrue(): self
    {
        $this->transaction->Result->Success = true;
        return $this;
    }

    public function setSuccessFalse(): self
    {
        $this->transaction->Result->Success = false;
        return $this;
    }
}