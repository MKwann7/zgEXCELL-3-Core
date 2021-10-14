<?php

namespace Entities\Cart\Classes\Helpers;

use Vendors\Stripe\Main\Stripe;
use Vendors\Stripe\Main\StripePayment;

class CartStripeCheckout
{
    private $totalCartValue;
    private $totalProductsValue;
    private $stripeAccountId;
    private $paymentMethodId;
    private $transactionResult;

    public function __construct ($totalCartValue, $totalProductsValue, $stripeAccountId, $paymentMethodId, &$transactionResult)
    {
        $this->totalCartValue = $totalCartValue;
        $this->totalProductsValue = $totalProductsValue;
        $this->stripeAccountId = $stripeAccountId;
        $this->paymentMethodId = $paymentMethodId;
        $this->transactionResult = $transactionResult;
    }

    public function process() : bool
    {
        $stripe = new Stripe();

        try
        {
            $stripePayment = new StripePayment($this->totalCartValue, $this->totalProductsValue, "usd", true);
            $paymentResult =  $stripe->createPaymentIntent($stripePayment, $this->stripeAccountId, $this->paymentMethodId);

            $this->transactionResult->getTransaction()->Result->Success = true;
            $this->transactionResult->getTransaction()->Result->Message = "Payment Processed successful.";
        }
        catch (\Exception $ex)
        {
            $this->errors->Add($ex->getMessage());
            $this->transactionResult->getTransaction()->Result->Success = false;
            $this->transactionResult->getTransaction()->Result->Message = $ex->getMessage();
            return false;
        }

        return true;
    }
}