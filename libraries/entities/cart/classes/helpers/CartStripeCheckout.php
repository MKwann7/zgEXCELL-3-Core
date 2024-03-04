<?php

namespace Entities\Cart\Classes\Helpers;

use App\Utilities\Excell\ExcellCollection;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Cart\Classes\CartProcessTransaction;
use Vendors\Stripe\Main\Stripe;
use Vendors\Stripe\Main\StripePayment;

class CartStripeCheckout
{
    private float $totalCartValue;
    private float $totalProductsValue;
    private float $processingFee;
    private $stripeAccountId;
    private $paymentMethodId;
    private CartProcessTransaction $transactionResult;
    private $errors;

    public function __construct (float $totalCartValue, float $totalProductsValue, float $processingFee, $stripeAccountId, $paymentMethodId, &$transactionResult)
    {
        $this->totalCartValue = $totalCartValue;
        $this->totalProductsValue = $totalProductsValue;
        $this->processingFee = $processingFee;
        $this->stripeAccountId = $stripeAccountId;
        $this->paymentMethodId = $paymentMethodId;
        $this->transactionResult = $transactionResult;
        $this->errors = new ExcellCollection();
    }

    public function process() : bool
    {
        $stripe = new Stripe();

        try
        {
            $stripePayment = new StripePayment($this->totalProcessingValue(), $this->totalProductsValue, "usd", true);
            $paymentResult =  $stripe->createPaymentIntent($stripePayment, $this->stripeAccountId, $this->paymentMethodId);

            $this->transactionResult->getTransaction()->getResult()->Success = true;
            $this->transactionResult->getTransaction()->getResult()->Message = "Payment Processed successful.";

            return true;
        }
        catch (\Exception $ex)
        {
            $this->errors->Add($ex->getMessage());
            $this->transactionResult->getTransaction()->getResult()->Success = false;
            $this->transactionResult->getTransaction()->getResult()->Message = $ex->getMessage();
            return false;
        }
    }

    protected function totalProcessingValue() : float
    {
        return $this->totalCartValue + $this->processingFee;
    }

    public function getErrors() : ExcellCollection
    {
        return $this->errors;
    }
}