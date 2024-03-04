<?php

namespace Entities\Cart\Classes;

use App\Core\App;
use App\entities\packages\models\PackageVariationModel;
use App\Utilities\Excell\ExcellCollection;
use Entities\Cart\Classes\Factories\CartProcessOptions;
use Entities\Cart\Classes\Helpers\CartMinimumValue;
use Entities\Cart\Classes\Helpers\CartStripeCheckout;
use Entities\Cart\Classes\Helpers\PackageSearch;
use Entities\Cart\Models\PromoCodeModel;
use Entities\Orders\Classes\OrderLines;
use Entities\Orders\Classes\Orders;
use Entities\Packages\Classes\Packages;
use Entities\Packages\Models\PackageLineModel;
use Entities\Payments\Classes\ArInvoices;
use Entities\Payments\Classes\PaymentAccounts;
use Entities\Payments\Classes\Transactions;
use Entities\Payments\Classes\UserPaymentProperty;
use Entities\Payments\Models\ArInvoiceModel;
use Entities\Payments\Models\TransactionModel;
use Entities\Orders\Models\OrderLineModel;
use Entities\Orders\Models\OrderModel;
use Entities\Products\Classes\Products;

class CartProcess
{
    protected int $userId;
    protected int $paymentAccountId;
    protected ExcellCollection $packages;
    protected int $companyId;
    protected int $defaultUserId;
    protected string $stripeAccountType;
    protected string|null $stripeAccountId;
    protected float $grossCartValue;
    protected float $grossProductsValue;
    protected float $processingFee;
    protected float $totalCartValue;
    protected float $totalProductsValue;
    protected float $netToGrossPercentage = 0;
    protected float $promoToTotalPercentage = 1;
    protected PromoCodeModel|null $promoCode = null;
    protected string|null $paymentMethodId = null;
    protected OrderModel|null $order = null;
    protected ArInvoiceModel|null $arInvoice = null;
    protected CartProcessTransaction|null $transactionResult = null;
    protected CartProcessOptions $cartProcessOptions;

    protected App $app;
    protected string $env;
    protected ExcellCollection $errors;
    protected bool $disableProcessing = false;
    protected PackageSearch $packageSearch;
    protected bool $noCharge = false;

    public function __construct()
    {
        global $app;
        $this->app = $app;
        $this->transactionResult = new CartProcessTransaction();
        $this->errors = new ExcellCollection();
        $this->packageSearch = new PackageSearch();
        $this->env = env("APP_ENV") === "production" ? "prod" : "test";
    }

    public function loadCart(array $arPackageIds, int $userId, int $paymentAccountId, CartProcessOptions $cartProcessOptions) : void
    {
        $this->cartProcessOptions = $cartProcessOptions;
        $this->userId = $userId;
        $this->paymentAccountId = $paymentAccountId;
        $this->companyId = $this->cartProcessOptions->company_id ?? (!empty($this->app->objCustomPlatform) ? $this->app->objCustomPlatform->getCompanyId() : 0);
        $this->defaultUserId = $this->cartProcessOptions->default_user_id ?? (!empty($this->app->objCustomPlatform) ? $this->app->objCustomPlatform->getCompany()->default_sponsor_id : 1001);

        $this->packages = $this->loadPackagesFromIds($arPackageIds, $cartProcessOptions);

        $stripeAccountType = !empty($this->app->objCustomPlatform) ? $this->app->objCustomPlatform->getCompanySettings()->FindEntityByValue("label", "stripe_account_type") : "customer";
        $this->stripeAccountType = $stripeAccountType->value ?? "customer";
        $this->stripeAccountId = $this->getCustomerStripeAccountId(1);

        if ($this->paymentAccountId !== 0) {
            $this->paymentMethodId = PaymentAccounts::getToken($this->paymentAccountId);
        }
    }

    public function processCheckout() : CartProcessTransaction
    {
        $this->processPackageLinesAndCreateTransactions();
        $this->processPromoCodeIfApplicable();
        $this->processProcessingFee();


        if (
            $this->totalCartValue <= 0 ||
            $this->noCharge === true ||
            (
                $this->paymentAccountId === 0 &&
                $this->env !== "prod"
            )
        ) {
            $this->disableProcessing = true;
        }

        if ($this->disableProcessing !== true && $this->paymentAccountId === 0 && $this->env === "prod") {
            $this->errors->Add("A payment account is required for this purchase.");
            return $this->processTransactionReturn();
        }

        $stripeCheckout = new CartStripeCheckout(
            $this->totalCartValue,
            $this->totalProductsValue,
            $this->processingFee,
            $this->stripeAccountId,
            $this->paymentMethodId,
            $this->transactionResult
        );

        if ($this->disableProcessing === false && !$stripeCheckout->process()) {
            $this->errors->Merge($stripeCheckout->getErrors());
            return $this->processTransactionReturn();
        }

        if (!$this->createArInvoice()) {
            return $this->processTransactionReturn();
        }

        if (!$this->createOrderAndOrderLinesForCart()) {
            return $this->processTransactionReturn();
        }

        if (!$this->savePackageLineTransactions()) {
            return $this->processTransactionReturn();
        }

        $this->transactionResult->getTransaction()->result->Success = true;
        $this->transactionResult->getTransaction()->result->Message = "Order processed successfully";

        return $this->processTransactionReturn();
    }

    private function loadPackagesFromIds($packageIds, CartProcessOptions $cartProcessOptions) : ExcellCollection
    {
        $packageQuantities = [];

        foreach($packageIds as $currPackage) {
            $arPackageIds[] = $currPackage["var_id"];
            $packageQuantities[$currPackage["var_id"]] = (float) $currPackage["quantity"];
        }

        // TODO - Change this to be a dependency so it can be unit tested.
        $packages = Packages::getFullPackagesByVariationIds($arPackageIds)->getData();

        $this->processOptionalWidgetsForPurchase($packages, $cartProcessOptions);

        $packages->Foreach(function (PackageVariationModel $currPackage) use ($cartProcessOptions)
        {
            $currPackage->lines->Foreach(function (PackageLineModel $currPackageLine) use ($cartProcessOptions)
            {
                if ($currPackageLine->product->abbreviation === "SITEPAGE" && $cartProcessOptions->page_create_count_override > 0) {
                    $currPackageLine->quantity = $cartProcessOptions->page_create_count_override - $cartProcessOptions->widgets_for_purchase->Count();
                }

                return $currPackageLine;
            });

            return $currPackage;
        });

        $CartMinValue = new CartMinimumValue();
        $CartMinValue->process($packages, $packageQuantities);

        return $packages;
    }

    private function processOptionalWidgetsForPurchase(ExcellCollection &$packages, CartProcessOptions $cartProcessOptions): void
    {
        $products = new Products();
        $cartProcessOptions->widgets_for_purchase->Foreach(function(PackageLineModel $packageLineModel) use (&$packages, $products) {
            $packageLineModel->AddUnvalidatedValue("product", $products->getById($packageLineModel->product_entity_id)->getData()->first());
            $packages->first()->lines->Add($packageLineModel);
        });
    }

    public function processPromoCode($promoCode) : void
    {
        $objPromoCodes   = new PromoCodes();
        $promoCodeResult = $objPromoCodes->getById($promoCode);

        if ($promoCodeResult->result->Count === 1) {
            $this->promoCode = $promoCodeResult->getData()->first();
            $this->transactionResult->promoCode = $this->promoCode;
        }
    }

    public function setNoCharge() : void
    {
        $this->noCharge = true;
    }

    private function createArInvoice() : bool
    {
        $objArInvoices = new ArInvoices();

        $arInvoice = new ArInvoiceModel();
        $arInvoice->company_id = $this->companyId;
        $arInvoice->division_id = 0;
        $arInvoice->user_id = $this->userId;
        $arInvoice->gross_value = $this->totalProcessingValue();
        //$arInvoice->net_value = $purchasePrice;
        $arInvoice->tax = 0;
        $arInvoice->payment_account_id = $this->paymentAccountId ?? EXCELL_NULL;
        $arInvoice->payment_type_id = 1; // stripe
        $arInvoice->status = "completed";
        $arInvoice->created_on    = $this->cartProcessOptions->creation_date_override ?? date("Y-m-d H:i:s");
        $arInvoice->last_updated    = $this->cartProcessOptions->creation_date_override ?? date("Y-m-d H:i:s");

        $arInvoiceResult = $objArInvoices->createNew($arInvoice);

        if ($arInvoiceResult->result->Success === false)
        {
            $this->errors->Add("ArInvoice unable to be processed: " . $arInvoiceResult->result->Message);
            return false;
        }

        $this->arInvoice = $arInvoiceResult->getData()->first();

        return true;
    }

    private function createOrderAndOrderLinesForCart(): bool
    {
        $objOrders = new Orders();

        $order = new OrderModel();
        $order->company_id = $this->companyId;
        $order->division_id = 0;
        $order->user_id = $this->userId;
        $order->total_price = $this->totalCartValue;
        $order->title = "Cart Purchase on " . date("Y-m-d") . " at " . date("H:i:s");
        $order->status = "started"; // TODO - Question for Cheryl
        $order->created_on = $this->cartProcessOptions->creation_date_override ?? date("Y-m-d H:i:s");
        $order->last_updated = $this->cartProcessOptions->creation_date_override ?? date("Y-m-d H:i:s");

        $orderResult = $objOrders->createNew($order);

        if ($orderResult->result->Success === false)
        {
            $this->errors->Add("Order unable to be processed: " . $orderResult->result->Message);
            return false;
        }

        $this->order = $orderResult->getData()->first();

        return $this->createOrderLinesForCart();
    }

    private function createOrderLinesForCart() : bool
    {
        if (empty($this->order))
        {
            $this->errors->Add("Order wasn't created successfully. Unable to create order lines.");
            return false;
        }

        $noErrors = true;
        $objOrderLines = new OrderLines();

        $this->loopThroughPackageLines(function(PackageLineModel $currPackageLine, PackageVariationModel $currPackage) use ($objOrderLines, &$noErrors)
        {
            $currPackageLine->entities->Foreach(function(CartProductCapsule $cartProductCapsule) use ($objOrderLines, &$noErrors, $currPackageLine)
            {
                $product = $cartProductCapsule->getProduct();
                $orderLine = new OrderLineModel();

                $orderLine->order_id = $this->order->order_id;
                $orderLine->product_id = $currPackageLine->product->product_id;
                $orderLine->company_id = $this->companyId;
                $orderLine->user_id = $this->userId;
                $orderLine->payment_account_id = $this->paymentAccountId ?? EXCELL_NULL;
                $orderLine->title = $currPackageLine->product->display_name;
                $orderLine->status = "started";
                $orderLine->billing_date = date("Y-m-d H:i:s");
                $orderLine->promo_price = $currPackageLine->promo_price;

                if ($this->stripeAccountType === "connected")
                {
                    $orderLine->promo_fee = $currPackageLine->product_promo_price_override ?? $product->promo_value;
                    $orderLine->price = $currPackageLine->regular_price;
                    $orderLine->price_fee = $currPackageLine->product_price_override ?? $product->value;
                }
                else
                {
                    $orderLine->promo_fee = 0;
                    $orderLine->price = $currPackageLine->regular_price;
                    $orderLine->price_fee = 0;
                }

                $orderLine->promo_duration = $product->promo_cycle_duration;
                $orderLine->price_duration = $product->value_duration;
                $orderLine->cycle_type = $product->cycle_type;
                $orderLine->created_on = $this->cartProcessOptions->creation_date_override ?? date("Y-m-d H:i:s");
                $orderLine->last_updated = $this->cartProcessOptions->creation_date_override ?? date("Y-m-d H:i:s");
                $orderLine->created_by = $this->defaultUserId ?? $this->userId;
                $orderLine->updated_by = $this->defaultUserId ?? $this->userId;

                $orderLineResult = $objOrderLines->createNew($orderLine);

                if ($orderLineResult->result->Success === false)
                {
                    $this->errors->Add("Order line unable to be processed: " . $orderLineResult->result->Message);
                    $noErrors = false;
                    return false;
                }

                $cartProductCapsule->setOrderLine($orderLineResult->getData()->first());

                return $cartProductCapsule;
            });

            return $currPackageLine;
        });

        return $noErrors;
    }

    private function savePackageLineTransactions() : bool
    {
        $transactions = new Transactions();
        $this->loopThroughPackageLines(function(PackageLineModel $currPackageLine, PackageVariationModel $currPackage) use ($transactions)
        {
            $currPackageLine->entities->Foreach(function(CartProductCapsule $cartProductCapsule) use ($transactions)
            {
                $transaction = $cartProductCapsule->getTransaction();
                $orderLine = $cartProductCapsule->getOrderLine();
                $packageLine = $cartProductCapsule->getPackageLine();

                $transaction->ar_invoice_id     = $this->arInvoice->ar_invoice_id;
                $transaction->order_id          = $this->order->order_id;
                $transaction->order_line_id     = $orderLine->order_line_id;
                $transaction->package_line_id   = $packageLine->package_line_id ?? 0;
                $transaction->created_on        = $this->cartProcessOptions->creation_date_override ?? date("Y-m-d H:i:s");

                $transactionResult              = $transactions->createNew($transaction);
                $transaction                    = $transactionResult->getData()->first();

                $cartProductCapsule->setTransaction($transaction);

                return $cartProductCapsule;
            });

            return $currPackageLine;
        });

        return true;
    }

    private function processProcessingFee() : void
    {
        $this->processingFee = sprintf ("%.2f", (($this->totalCartValue) *  0.0298662) + .3);
    }

    private function processPromoCodeIfApplicable() : void
    {
        $this->totalCartValue = $this->grossCartValue;

        if ($this->promoCode === null)
        {
            $this->totalProductsValue = $this->grossProductsValue;
            return;
        }

        switch($this->promoCode->discount_type)
        {
            case "%":
                $this->totalCartValue *= ($this->promoCode->discount_value / 100);
                break;
            default:
                $this->totalCartValue -= $this->promoCode->discount_value;
                break;
        }

        $this->promoToTotalPercentage = (!empty($this->totalCartValue) && !empty($this->grossCartValue)) ? ($this->totalCartValue / $this->grossCartValue) : 0;
        $this->totalProductsValue = $this->promoToTotalPercentage * $this->grossProductsValue;
        $this->processPromoCodeOnEachPackageLineTransaction();

        $this->totalCartValue = round($this->totalCartValue, 2);
        $this->totalProductsValue = round($this->totalProductsValue, 2);
    }

    private function loopThroughPackageLines($callback) : void
    {
        $this->packages->Foreach(function(PackageVariationModel $currPackage) use ($callback)
        {
            return $this->packageSearch->loopThroughLinesFromPackageRecord($currPackage, $callback);
        });
    }

    private function processPackageLinesAndCreateTransactions() : void
    {
        $totalCartValue = 0;
        $totalProductsValue = 0;

        $this->loopThroughPackageLines(function(PackageLineModel $currPackageLine, PackageVariationModel $currPackage) use (&$totalCartValue, &$totalProductsValue)
        {
            /** @var $currPackageLine->entities ExcellCollection */
            $currPackageLine->AddUnvalidatedValue("entities", new ExcellCollection());
            $quantity = ($currPackageLine->quantity ?? 1) * ($currPackage->cart_quantity ?? 1);

            for ($currPackageLineIndex = 0; $currPackageLineIndex < $quantity; $currPackageLineIndex++) {
                $transaction = new TransactionModel();
                $transaction->company_id          = $this->companyId;
                $transaction->division_id         = 0;
                $transaction->package_id          = $currPackage->package_id;
                $transaction->package_line_id     = $currPackageLine->package_line_id;
                $transaction->product_entity      = $currPackageLine->product_entity;
                $transaction->product_entity_id   = $currPackageLine->product_entity_id;
                $transaction->user_id             = $this->userId;
                $transaction->tax                 = 0;
                $transaction->transaction_type_id = 1;
                $transaction->created_on          = $this->cartProcessOptions->creation_date_override ?? date("Y-m-d H:i:s");
                $transaction->gross_value         = $this->processGrossValueFromPackageAndProduct($currPackageLine, $totalProductsValue);

                $totalCartValue += $transaction->gross_value;

                $productCapsule = new CartProductCapsule();
                $productCapsule->setTransaction($transaction)->setProduct($currPackageLine->product)->setPackageLine(new PackageLineModel($currPackageLine));

                $currPackageLine->entities->Add($productCapsule);
            }

            return $currPackageLine;
        });

        $this->grossCartValue = $totalCartValue;
        $this->grossProductsValue = $totalProductsValue;

        if ($this->stripeAccountType === "connected") {
            $this->netToGrossPercentage = !empty($this->grossProductsValue) ? ($this->grossProductsValue / $this->grossCartValue) : 0;
        }
    }

    private function processPromoCodeOnEachPackageLineTransaction() : void
    {
        $this->packages->Foreach(function(PackageVariationModel $currPackage) use (&$totalCartValue)
        {
            if (empty($currPackage->lines) || !is_a($currPackage->lines, ExcellCollection::class)) { return; }

            $currPackage->lines->Foreach(function(PackageLineModel $currPackageLine)
            {
                if (empty($currPackageLine->entities)) { return; }

                $currPackageLine->entities->Foreach(function(CartProductCapsule $cartProductCapsule)
                {
                    if (empty($cartProductCapsule->transaction) || empty($cartProductCapsule->transaction->gross_value)) { return; }

                    $cartProductCapsule->transaction->gross_value = round($cartProductCapsule->transaction->gross_value * $this->promoToTotalPercentage, 2);

                    return $cartProductCapsule;
                });

                return $currPackageLine;
            });

            return $currPackage;
        });
    }

    private function processGrossValueFromPackageAndProduct(PackageLineModel $packageLine, &$totalProductsValue) : float
    {
        $packageValue = ($packageLine->promo_price > 0 ? $packageLine->promo_price : $packageLine->regular_price);
        $totalProductsValue += ($packageLine->product_promo_price_override ?? $packageLine->product_price_override ?? $packageLine->product->value);

        return (float) $packageValue;
    }

    private function getBillingAccountId($userId) : int
    {
        $globalBillingAccountId = !empty($this->app->objCustomPlatform) ? $this->app->objCustomPlatform->getCompanySettings()->FindEntityByValue("label", "global_billing_account_id") : 0;
        return (int) !empty($globalBillingAccountId) ? $globalBillingAccountId->value : $userId;
    }

    private function getCustomerStripeAccountId($typeId = 1) : ?string
    {
        $intBillingUserAccountId = $this->getBillingAccountId($this->userId);
        $paymentPropertyResult = (new UserPaymentProperty())->getWhere(["user_id" => $intBillingUserAccountId, "company_id" => $this->companyId, "state" => $this->env, "type_id" => $typeId]);
        if ($paymentPropertyResult->result->Count === 0) { return null; }
        return $paymentPropertyResult->getData()->first()->value;
    }

    private function processTransactionReturn() : CartProcessTransaction
    {
        if ($this->errors->Count() > 0)
        {
            $this->transactionResult->setErrors($this->errors);
        }
        else
        {
            $this->transactionResult->setPackages($this->packages)->setSuccessTrue();
            $this->transactionResult->order = $this->order;
            $this->transactionResult->arInvoice = $this->arInvoice;
            $this->transactionResult->userId = $this->userId;
            $this->transactionResult->companyId = $this->companyId;
            $this->transactionResult->defaultUserId = $this->defaultUserId;

            $this->transactionResult->totalCartValue = $this->totalCartValue;
            $this->transactionResult->totalProductsValue = $this->totalProductsValue;
        }

        return $this->transactionResult;
    }

    protected function totalProcessingValue() : float
    {
        return $this->totalCartValue + $this->processingFee;
    }
}