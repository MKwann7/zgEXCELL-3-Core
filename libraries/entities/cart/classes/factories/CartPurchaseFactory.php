<?php

namespace Entities\Cart\Classes\Factories;

use App\Core\Abstracts\AbstractFactory;
use App\Core\App;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Cards\Classes\Cards;
use Entities\Cart\Classes\CartEmails;
use Entities\Cart\Classes\CartProcess;
use Entities\Cart\Classes\CartTicketProcess;
use Entities\Products\Classes\ProductProcessor;

class CartPurchaseFactory extends AbstractFactory
{
    private App $app;
    private ProductProcessor $productProcessor;
    private CartProcess $cartProcess;
    private CartTicketProcess $cartTicketProcess;
    private CartEmails $cartEmails;
    private Cards $cards;
    private $noCharge = false;

    public function __construct(
        CartProcess $cartProcess,
        ProductProcessor $productProcessor,
        CartTicketProcess $cartTicketProcess,
        CartEmails $cartEmails,
        Cards $cards
    )
    {
        global $app;
        $this->app = $app;
        $this->cartProcess = $cartProcess;
        $this->productProcessor = $productProcessor;
        $this->cartTicketProcess = $cartTicketProcess;
        $this->cartEmails = $cartEmails;
        $this->cards = $cards;
    }

    public function processShoppingCart(array $packageIds, int $promoCode,  int $userId, int $paymentAccountId, CartProcessOptions $cartProcessOptions) : ExcellTransaction
    {
        $parentEntity = null;
        if ($cartProcessOptions->parent_entity_type === "card") {
            $parentEntity = $this->cards->getById($cartProcessOptions->parent_entity_id)->getData()->first();
        }

        $this->cartProcess->loadCart($packageIds, $userId, $paymentAccountId, $cartProcessOptions);

        if ($this->noCharge === true) {
            $this->cartProcess->setNoCharge();
        } elseif ($promoCode !== 0) {
            $this->cartProcess->processPromoCode($promoCode);
        }

        $cartProcessTransaction = $this->cartProcess->processCheckout();

        if ($cartProcessTransaction->getTransaction()->result->Success !== true) {
            return $this->processReturn(false, ["errors" => $cartProcessTransaction->getErrors()->ToPublicArray()], $cartProcessTransaction->getTransaction()->result->Message);
        }

        $this->productProcessor->loadCartProcess($cartProcessTransaction, $cartProcessOptions);

        if (!$this->productProcessor->processLoadedProducts($parentEntity)) {
            return $this->processReturn(false, ["errors" => $this->productProcessor->generateProductCreationErrors()], "There was an error processing your order.");
        }

        $this->cartTicketProcess->loadProductProcessor($this->productProcessor);
        $this->cartTicketProcess->registerTickets();

        if ($cartProcessOptions->skip_emails !== true) {
            //$this->cartEmails->loadProductProcessor($this->productProcessor);
            //$this->cartEmails->sendEmails();
        }

        return $this->processReturn(true);
    }

    public function setFreePersonaPurchase() : self
    {
        $this->noCharge = true;
        return $this;
    }

    public function getProductProcessor() : ProductProcessor
    {
        return $this->productProcessor;
    }
}