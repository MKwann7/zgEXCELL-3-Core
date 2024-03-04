<?php

namespace Entities\Products\Classes;

use App\Core\App;
use App\entities\packages\models\PackageVariationModel;
use App\Utilities\Excell\ExcellCollection;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Cards\Classes\CardAddon;
use Entities\Cards\Classes\CardDomains;
use Entities\Cards\Classes\CardPage;
use Entities\Cards\Classes\CardPageRels;
use Entities\Cards\Models\CardDomainModel;
use Entities\Cart\Classes\Factories\CartProcessOptions;
use Entities\Directories\Classes\Directories;
use Entities\Directories\Models\DirectoryModel;
use Entities\Modules\Classes\AppInstanceRels;
use Entities\Modules\Classes\AppInstances;
use Entities\Cards\Classes\Cards;
use Entities\Cards\Models\CardAddonModel;
use Entities\Cards\Models\CardModel;
use Entities\Cards\Models\CardPageModel;
use Entities\Cards\Models\CardPageRelModel;
use Entities\Cart\Classes\CartProcessTransaction;
use Entities\Cart\Classes\CartProductCapsule;
use Entities\Modules\Classes\ModuleApps;
use Entities\Modules\Models\AppInstanceModel;
use Entities\Modules\Models\AppInstanceRelModel;
use Entities\Orders\Classes\OrderLines;
use Entities\Orders\Models\OrderLineModel;
use Entities\Packages\Classes\PackageLineSettings;
use Entities\Packages\Models\PackageLineModel;
use Entities\Users\Classes\Users;
use Entities\Users\Models\UserModel;

class ProductProcessor
{
    public CartProcessTransaction $cartProcessTransaction;
    protected CartProcessOptions $cartProcessOptions;
    public UserModel $user;
    public ExcellCollection $cartItems;
    public ExcellCollection $productLineAttributes;
    private CardModel $referralCard;

    public int $companyId;
    public int $defaultUserId;

    public const CardTypeId = 1;
    const PRODUCT_ID = 1;
    const DESIGN_ID = 2;
    const PAGE_ID = 3;
    const WIDGET_ID = 5;
    const MODULE_ID = 6;
    const USER_PRODUCT_ID = 7;
    const ACCOUNT_PRODUCT_ID = 8;

    public function __construct()
    {
        $this->cartItems = new ExcellCollection();
    }

    public function loadCartProcess(
        CartProcessTransaction $cartProcessTransaction,
        CartProcessOptions $cartProcessOptions
    ) : void {
        $this->cartProcessTransaction = $cartProcessTransaction;
        $this->cartProcessOptions = $cartProcessOptions;
        $this->user = $this->getUserAccount($cartProcessTransaction->userId);
        $this->companyId = $cartProcessTransaction->companyId;
        $this->defaultUserId = $cartProcessTransaction->defaultUserId;
    }

    public function processLoadedProducts($parentEntity = null) : bool
    {
        $this->getProductLineAttributes();

        // $this->cartItems isn't being used in the second checkout...
        // IT needs to be, so we can iterate over it, and use it in a ticketing process.

        if (empty($parentEntity))
        {
            $this->processCartParentItems();
            $this->processChildItems();

            return true;
        }

        dd("no parent");

        // mock up cart item with Parent Entity and process against that
        $this->processItemsAgainstParentEntity($parentEntity);

        return true;
    }

    public function getCartProcessTransaction() : CartProcessTransaction
    {
        return $this->cartProcessTransaction;
    }

    public function getCartItems() : ExcellCollection
    {
        return $this->cartItems;
    }

    public function getUser() : UserModel
    {
        return $this->user;
    }

    private function getUserAccount($userId) : UserModel
    {
        return (new Users())->getFks(["user_email","user_phone"])->getById($userId)->getData()->first();
    }

    private function processCartParentItems() : void
    {
        // TODO - Change for having ProductType handed in via the shopping cart.
        // Right now, this only/always hands in a card, so we are setting it by default.
        $colCardPackageLine = $this->findProductCapsulesById();

        $colCardPackageLine->Foreach(function(CartProductCapsule $currProductCapsule)
        {
            // This will retrieve an existing/new card from createNewCardFromOrder.
            // We are also adding card_domain to this.
            $instantiatedProductResult = $this->processCartItemByProductId($currProductCapsule);

            if ($instantiatedProductResult->result->Success === false) { return; }

            $currProductCapsule->setProductInstantiation($instantiatedProductResult->getData()->first());

            $this->cartItems->Add($currProductCapsule);
        });
    }

    /**
     * @param CartProductCapsule $cartProductCapsule
     * @param CartProductCapsule|null $cartParentProductCapsule
     * @param bool $hidden
     * @return ExcellTransaction
     */
    private function processCartItemByProductId(
        CartProductCapsule &$cartProductCapsule,
        CartProductCapsule|null &$cartParentProductCapsule = null,
        bool $hidden = false
    ) : ExcellTransaction
    {
        $product = $cartProductCapsule->getProduct();

        switch($product->product_type_id) {
            case self::PRODUCT_ID: // Site
                $cardResult = $this->createNewCardFromOrder($cartProductCapsule);

                $card = $cardResult->getData()->first();

                $this->installCardTemplate($card, $cartProductCapsule->getPackageLine());
                $this->registerCardPagesForMoving($card);

                $cartProductCapsule->setProcessed(true);
                return new ExcellTransaction(true, "We got it.", (new ExcellCollection())->Add($card));

            case self::DESIGN_ID: // Design
                $cartProductCapsule->setProcessed(true);
                return $this->addDesignPackageToCard($cartProductCapsule, $cartParentProductCapsule->getProductInstantiation());

            case self::PAGE_ID: // Site Page
                $card = $cartParentProductCapsule->getProductInstantiation();
                $result = $this->addCardPageToCard($cartProductCapsule, $card, $hidden);
                $cartParentProductCapsule->setProductInstantiation($card);
                $cartProductCapsule->setProcessed(true);
                return $result;

            case self::MODULE_ID: // Module
                $moduleResult = $this->createModuleFromOrder($cartProductCapsule);

                $module = $moduleResult->getData()->first();

                $cartProductCapsule->setProcessed(true);
                return new ExcellTransaction(true, "We got it.", (new ExcellCollection())->Add($module));

            case self::USER_PRODUCT_ID: // User Product
                $userProductResult = $this->createCustomUserProductFromOrder($cartProductCapsule);
                $userProduct = $userProductResult->getData()->first();

                $cartProductCapsule->setProcessed(true);
                return new ExcellTransaction(true, "We got it.", (new ExcellCollection())->Add($userProduct));

            case self::ACCOUNT_PRODUCT_ID: // Account Product
                $accountProductResult = $this->createUserAccountProductFromOrder($cartProductCapsule);
                $accountProduct = $accountProductResult->getData()->first();

                $cartProductCapsule->setProcessed(true);
                return new ExcellTransaction(true, "We got it.", (new ExcellCollection())->Add($accountProduct));
        }

        return new ExcellTransaction(false);
    }

    /**
     * @param CartProductCapsule $cartProductCapsule
     * @param CartProductCapsule|null $cartParentProductCapsule
     * @param bool $hidden
     * @return void
     */
    private function processCartChildItemByProductId(
        CartProductCapsule &$cartProductCapsule,
        ?CartProductCapsule &$cartParentProductCapsule = null,
        bool $hidden = false
    ) : void
    {
        $product = $cartProductCapsule->getProduct();

        switch($product->product_type_id) {
            case self::WIDGET_ID: // This is a WIDGET
                // Widget This is attached to a product ATM. Will we charge for widgets?
                // If so, we will need to bind these products to a page?
                // Or... the page will need to reference them, and they are created without cost?
                $card = $cartParentProductCapsule->getProductInstantiation();

                // This creates a page. We don't need to do this?
                //
                $result = $this->addWidgetToCard($cartProductCapsule, $card, $hidden);
                $cartParentProductCapsule->setProductInstantiation($card);
                $cartProductCapsule->setProcessed(true);
                return;
        }

        new ExcellTransaction(false);
    }

    private function processChildItems() : void
    {
        $this->cartItems->Foreach(function(CartProductCapsule $currParentProductCapsule)
        {
            $colChildCartItemsFromParentId = $this->findChildItemsByCartItemId($currParentProductCapsule->cartItem, $currParentProductCapsule->getProductInstantiation());

            $colChildCartItemsFromParentId->Foreach(function(CartProductCapsule $currChildProductCapsule) use (&$currParentProductCapsule) {
                $this->processCartItemByProductId($currChildProductCapsule,$currParentProductCapsule);
                return $currChildProductCapsule;
            });

            // This actually is generating modules. This is not what we want.
            // This needs to assign widgets to pages, if there is a viable module.
            return $this->processWidgetsAgainstCartCapsule($currParentProductCapsule);
        });
    }

    private function processItemsAgainstParentEntity(CardModel $cardModel) : void
    {
        // The findChildItemsByCartItemId doesn't search for a parent cart id, just grabs them all ---- YET
        $colChildCartItemsFromParentId = $this->findChildItemsByCartItemId(null, $cardModel);

        $currProductCapsule = new CartProductCapsule();
        $this->registerCardPagesForMoving($cardModel);
        $currProductCapsule->setProductInstantiation($cardModel);

        $colChildCartItemsFromParentId->Foreach(function(CartProductCapsule $currChildProductCapsule) use (&$currProductCapsule)
        {
            $this->processCartItemByProductId($currChildProductCapsule,$currProductCapsule, true);
            return $currChildProductCapsule;
        });

        $this->processWidgetsAgainstCartCapsule($currProductCapsule, true);
    }

    private function processWidgetsAgainstCartCapsule(CartProductCapsule $currProductCapsule, $hidden = false) : CartProductCapsule
    {
        // This will eventually be handled by $currPRoductCapsule->cartItem->cart_item_id;
        $colChildCartItemsFromParentId = $this->findChildItemsByCartItemId(
            $currProductCapsule->cartItem,
            $currProductCapsule->getProductInstantiation()
        );

        $colChildCartItemsFromParentId->Foreach(function(CartProductCapsule $currChildProductCapsule) use (&$currProductCapsule, $hidden) {
            $this->processCartChildItemByProductId(
                $currChildProductCapsule,
                $currProductCapsule,
                $hidden
            );

            return $currChildProductCapsule;
        });

        $this->movePagesAfterIndex($currProductCapsule->getProductInstantiation());

        return $currProductCapsule;
    }

    private function movePagesAfterIndex(?CardModel $card) : void
    {
        if (empty($card) || empty($card->pagesToMove) || $card->pagesToMove->Count() === 0)
        {
            return;
        }

        $newCardPageCount = $card->cardPages->Count();
        $objCardPages = new CardPageRels();

        $card->pagesToMove->Each(static function(CardPageRelModel $currPage) use ($newCardPageCount, $objCardPages)
        {
            $currPage->rel_sort_order += $newCardPageCount;
            $objCardPages->update($currPage);
        });
    }

    private function findChildItemsByCartItemId($cartItem = null, $productInstantiation = null) : ExcellCollection
    {
        // This will eventually match up the cart items by the cart item id (cartItem->cart_item_id).
        // Right now, we are only getting the items that were not just processed (the only card in the cart),
        // And attached them to a collection.
        $colChildItems = new ExcellCollection();

        $this->loopThroughPackageLines(function (PackageLineModel $currPackageLine, PackageVariationModel $currPackage) use (&$colChildItems, $productInstantiation) {
            $currPackageLine->entities->Foreach(function (CartProductCapsule $cartProductCapsule) use (&$colChildItems, $productInstantiation) {
                if ($cartProductCapsule->processed === true) {
                    return;
                }

                $cartProductCapsule->setParentEntity("card", $productInstantiation->getId());
                $colChildItems->Add($cartProductCapsule);

                return $cartProductCapsule;
            });

            return $currPackageLine;
        });

        return $colChildItems;
    }

    private function createNewCardFromOrder(CartProductCapsule $cartProductCapsule) : ExcellTransaction
    {
        $product = $cartProductCapsule->getProduct();
        $packageLine = $cartProductCapsule->getPackageLine();
        $orderLine = $cartProductCapsule->getOrderLine();
        $orderLineId = $orderLine->order_line_id;

        $cards = new Cards();
        $objCardNumCheck = $cards->getWhere(null, "card_num.DESC", 1)->getData()->first();
        $intNewCardNum = !empty($objCardNumCheck) ? ($objCardNumCheck->card_num + 1) : 1 ;

        global $app;
        $siteType = $this->renderProductType((int) $product->product_id);
        $siteTypeName = !empty($app->objCustomPlatform) ? ($app->objCustomPlatform->getCompanySettings()->FindEntityByValue("label", "application_type")->value ==="default" ? "Card" : "Site") : "Site";
        $siteTemplate = $this->productLineAttributes->FindEntityByValues(["package_line_id" => $packageLine->package_line_id, "label" => "default_site_template_id"]);
        $siteTemplateId = ($siteTemplate !== null) ? $siteTemplate->value : 1;

        $objCardCreate = new CardModel();
        $objCardCreate->owner_id = $this->user->user_id;
        $objCardCreate->card_user_id = $this->user->user_id;
        $objCardCreate->company_id = $this->companyId;
        $objCardCreate->division_id = 0;
        $objCardCreate->card_name = $siteTypeName . " for {$this->user->first_name} {$this->user->last_name} - {$this->user->user_id}";
        $objCardCreate->product_id = $product->product_id;
        $objCardCreate->status = "Build";
        $objCardCreate->template_id = $siteTemplateId;
        $objCardCreate->template_card = EXCELL_FALSE;
        $objCardCreate->card_type_id = $siteType;
        $objCardCreate->card_num = $intNewCardNum;
        $objCardCreate->created_by = $this->defaultUserId;
        $objCardCreate->updated_by = $this->defaultUserId;
        $objCardCreate->created_on = $this->cartProcessOptions->creation_date_override ?? date("Y-m-d H:i:s");
        $objCardCreate->last_updated = $this->cartProcessOptions->creation_date_override ?? date("Y-m-d H:i:s");
        $objCardCreate->order_line_id = $orderLineId;

        // We need to create this, then also create a cardDomain entity.
        $newCardResult = $cards->createNew($objCardCreate);

        if ($newCardResult->getResult()->Success === false) {
            return new ExcellTransaction(false, $newCardResult->getResult()->Message,null, 0, $newCardResult->getResult()->Errors);
        }

        $card = $newCardResult->getData()->first();

        $cardDomain = new CardDomains();
        $result = $cardDomain->createNew(new CardDomainModel([
            "card_id" => $card->card_id,
            "type" => $this->getTypeFromCardType($objCardCreate->card_type_id)
        ]));

        return $newCardResult;
    }

    private function createModuleFromOrder(CartProductCapsule $cartProductCapsule) : ExcellTransaction
    {
        $product = $cartProductCapsule->getProduct();
        $orderLine = $cartProductCapsule->getOrderLine();
        $orderLineId = $orderLine->order_line_id;

        if ($product->product_type_id !== 6) {
            return new ExcellTransaction(false, $product->title . " is not a module.",null, 0, [$product->title . " is not a module."]);
        }

        $result = null;

        switch($product->product_id) {
            case 1006: // The Directory
                $result = $this->createDirectoryFromOrder($cartProductCapsule);
                break;
            default:
                $result = new ExcellTransaction(false, "No product match for ID: " . $product->product_id);
                break;
        }

        return $result;
    }

    private function createDirectoryFromOrder(CartProductCapsule $cartProductCapsule) : ExcellTransaction
    {
        /** @var App $app  */
        global $app;
        $product = $cartProductCapsule->getProduct();

        $directories = new Directories();
        $directoryModel = new DirectoryModel();
        $directoryModel->company_id = $app->objCustomPlatform->getCompanyId();
        $directoryModel->division_id = 0;
        $directoryModel->card_id = $this->getUser()->user_id;
        $directoryModel->user_id = $this->getUser()->user_id;
        $directoryModel->type_id = 1;
        $directoryModel->template_id = 1; // This may default to a company_settings value in the future
        $directoryModel->title = "My Directory";
        $directoryModel->instance_uuid = $product->source_uuid;

        return $directories->createNew($directoryModel);
    }

    private function renderProductType(int $productId) : string
    {
        // We might be able to do this in the database?
        return match ($productId) {
            1004 => 2,
            1005 => 3,
            default => 1,
        };
    }

    private function getTypeFromCardType($type) : string
    {
        switch($type) {
            case 2:
                return "persona";
            case 3:
                return "group";
            default:
                return "card";
        }
    }

    private function addDesignPackageToCard(CartProductCapsule $cartProductCapsule, CardModel $card) : ExcellTransaction
    {
        $product = $cartProductCapsule->getProduct();
        $orderLine = $cartProductCapsule->getOrderLine();

        $objCardAddon = new CardAddon();

        $cardAddon = new CardAddonModel();
        $cardAddon->company_id = $this->companyId;
        $cardAddon->division_id = 0;
        $cardAddon->user_id = $card->owner_id;
        $cardAddon->card_id = $card->card_id;
        $cardAddon->order_line_id = $orderLine->order_line_id;
        $cardAddon->order_id = $orderLine->order_id;
        $cardAddon->product_type_id = $product->product_type_id;
        $cardAddon->product_id = $product->product_id;
        $cardAddon->status = "active";
        $cardAddon->created_on = $this->cartProcessOptions->creation_date_override ?? date("Y-m-d");
        $cardAddon->last_updated = $this->cartProcessOptions->creation_date_override ?? date("Y-m-d");

        return $objCardAddon->createNew($cardAddon);
    }

    protected function addCardPageToCard(CartProductCapsule $cartProductCapsule, CardModel &$card, $hidden = false) : ExcellTransaction
    {
        $currProduct = $cartProductCapsule->getProduct();
        $orderLine = $cartProductCapsule->getOrderLine();
        $packageLineId = $cartProductCapsule->getPackageLine()->package_line_id;

        if (empty($card->cardPages)) { $card->AddUnvalidatedValue("cardPages", new ExcellCollection()); }

        $cardPageIndex = $card->page_insertion_index + ($card->cardPages->Count() - 1);
        $newCardPage = new \stdClass();
        $objCardPage = new CardPageModel();
        $objCardPage->user_id = $card->owner_id;
        $objCardPage->company_id = $this->companyId;
        $objCardPage->division_id = 0;
        $objCardPage->card_tab_type_id = 1; // Defaulting to HTML page
        $objCardPage->title = "Untitled Page " . $cardPageIndex;
        $objCardPage->menu_title = "Untitled-" . $cardPageIndex;
        $objCardPage->url = "untitled-" . $cardPageIndex;
        $objCardPage->library_tab = EXCELL_FALSE;
        $objCardPage->permanent = EXCELL_FALSE;
        $objCardPage->order_number = $cardPageIndex;
        $objCardPage->visibility = ($hidden === false ? EXCELL_TRUE : EXCELL_FALSE );
        $objCardPage->created_by = $this->defaultUserId;
        $objCardPage->updated_by = $this->defaultUserId;
        $objCardPage->created_on = $this->cartProcessOptions->creation_date_override ?? date("Y-m-d");
        $objCardPage->last_updated = $this->cartProcessOptions->creation_date_override ?? date("Y-m-d");

        $pageContent = "";

        if ($this->productLineAttributes->FindEntityByValues(["package_line_id" => $packageLineId, "label" => "page_content"]) !== null)
        {
            $pageContent = base64_encode($this->productLineAttributes->FindEntityByValues(["package_line_id" => $packageLineId, "label" => "page_content"])->value);
        }

        $objCardPage->content = $pageContent;

        $objNewCardPageResult = (new CardPage())->getFks()->createNew($objCardPage);

        $newCardPage->page = $objNewCardPageResult->getData()->first();
        $newCardPage->card_tab_id = $objNewCardPageResult->getData()->first()->card_tab_id;
        $newCardPage->id = $card->cardPages->Count() + 1;

        $objCardAddon = new CardAddon();

        $cardAddon = new CardAddonModel();
        $cardAddon->company_id = $this->companyId;
        $cardAddon->division_id = 0;
        $cardAddon->user_id = $card->owner_id;
        $cardAddon->card_id = $card->card_id;
        $cardAddon->order_line_id = $orderLine->order_line_id;
        $cardAddon->order_id = $orderLine->order_id;
        $cardAddon->product_type_id = $currProduct->product_type_id;
        $cardAddon->product_id = $currProduct->product_id;
        $cardAddon->status = "active";
        $cardAddon->created_on = $this->cartProcessOptions->creation_date_override ?? date("Y-m-d");
        $cardAddon->last_updated = $this->cartProcessOptions->creation_date_override ?? date("Y-m-d");

        $newCardPage->cardAddon = $objCardAddon->createNew($cardAddon)->getData()->first();

        $objCardPageRelResult = new CardPageRelModel();
        $objCardPageRelResult->card_tab_id = $newCardPage->page->card_tab_id;
        $objCardPageRelResult->card_id = $card->card_id;
        $objCardPageRelResult->user_id = $card->owner_id;
        $objCardPageRelResult->rel_sort_order = $cardPageIndex;
        $objCardPageRelResult->rel_visibility = ($hidden === false ? EXCELL_TRUE : EXCELL_FALSE );
        $objCardPageRelResult->card_tab_rel_type = "default";
        $objCardPageRelResult->card_addon_id = $newCardPage->cardAddon->card_addon_id;
        $objCardPageRelResult->order_line_id =  $orderLine->order_line_id;

        $objNewCardPageRelResult = (new CardPageRels())->getFks()->createNew($objCardPageRelResult);
        $newCardPage->pageRel = $objNewCardPageRelResult->getData()->first();


        $newCardPage->processed = false;

        $card->cardPages->Add($newCardPage);

        return new ExcellTransaction(true, "Page Created", (new ExcellCollection())->Add($newCardPage));
    }

    protected function createNewCardPageForWidget(&$card, CartProductCapsule &$cartProductCapsule, $hidden = false) : ExcellTransaction
    {
        $product = (new Products())->getById(1002)->getData()->first();
        $product->AddUnvalidatedValue("orderLine", $cartProductCapsule->getOrderLine());

        // We need to create a new order line here and assign it to the OrderLine on the CartProductCapsule
        // This is to create a new orderline for the app instance on the page.
        $objOrderLines = new OrderLines();
        $orderLineForAppInstance = new OrderLineModel();
        $orderLineForAppInstance->order_id = $cartProductCapsule->getOrderLine()->order_id;
        $orderLineForAppInstance->product_id = $product->product_id;
        $orderLineForAppInstance->company_id = $cartProductCapsule->getOrderLine()->company_id;
        $orderLineForAppInstance->user_id = $cartProductCapsule->getOrderLine()->user_id;
        $orderLineForAppInstance->payment_account_id = $cartProductCapsule->getOrderLine()->paymentAccountId ?? EXCELL_NULL;
        $orderLineForAppInstance->title = $cartProductCapsule->getOrderLine()->display_name . " Page";
        $orderLineForAppInstance->status = "started";
        $orderLineForAppInstance->billing_date = $this->cartProcessOptions->creation_date_override ?? date("Y-m-d H:i:s");

        // TODO - This will need to be updated when we have widget billing
        $orderLineForAppInstance->promo_price = 0;
        $orderLineForAppInstance->promo_fee = 0;
        $orderLineForAppInstance->price = 0;
        $orderLineForAppInstance->price_fee = 0;

        $orderLineForAppInstance->promo_duration = $product->promo_cycle_duration;
        $orderLineForAppInstance->price_duration = $product->value_duration;
        $orderLineForAppInstance->cycle_type = $product->cycle_type;
        $orderLineForAppInstance->created_by = $this->defaultUserId;
        $orderLineForAppInstance->updated_by = $this->defaultUserId;

        $orderLineAppResult = $objOrderLines->createNew($orderLineForAppInstance);

        $cartProductCapsole = new CartProductCapsule();
        $cartProductCapsole->setProduct($product);
        $cartProductCapsole->setOrderLine($orderLineAppResult->getData()->first());
        $cartProductCapsole->setPackageLine($cartProductCapsule->getPackageLine());

        return $this->addCardPageToCard($cartProductCapsole,$card, $hidden);
    }

    protected function addWidgetToCard(CartProductCapsule &$cartProductCapsule, CardModel &$card, $hidden = false) : ExcellTransaction
    {
        $currProduct = $cartProductCapsule->getProduct();
        $orderLine = $cartProductCapsule->getOrderLine();

        $objPageResult = $this->createNewCardPageForWidget($card, $cartProductCapsule, $hidden);

        if ($objPageResult->result->Success === false)
        {
            return new ExcellTransaction(false);
        }

        $objPage = $objPageResult->getData()->first();

        if (empty($card->cardPageUsedCount)) { $card->AddUnvalidatedValue("cardPageUsedCount", 0); }

        $objModuleApps = new ModuleApps();
        $moduleAppResult = $objModuleApps->getLatestModuleAppsByUuid($currProduct->source_uuid);

        $moduleApp = $moduleAppResult->getData()->first();
        $moduleAppWidget = $moduleApp->widgets->FindEntityByValue("widget_class", 1004);

        $objAppInstance = new AppInstanceModel();
        $objAppInstance->owner_id = $card->owner_id;
        $objAppInstance->module_app_id = $moduleApp->module_app_id;
        $objAppInstance->order_line_id = $orderLine->order_line_id;
        $objAppInstance->product_id = $currProduct->product_id;
        $objAppInstance->instance_uuid = getGuid();

        $appInstanceResult = (new AppInstances())->getFks()->createNew($objAppInstance);
        $appInstance = $appInstanceResult->getData()->first();

        // This is to create a new orderline for the widget instance on the page.
        $objOrderLines = new OrderLines();
        $orderLineForWidgetInstance = new OrderLineModel();
        $orderLineForWidgetInstance->order_id = $orderLine->order_id;
        $orderLineForWidgetInstance->product_id = $currProduct->product_id;
        $orderLineForWidgetInstance->company_id = $orderLine->company_id;
        $orderLineForWidgetInstance->user_id = $orderLine->user_id;
        $orderLineForWidgetInstance->payment_account_id = $orderLine->paymentAccountId ?? EXCELL_NULL;
        $orderLineForWidgetInstance->title = $currProduct->display_name . " Widget";
        $orderLineForWidgetInstance->status = "started";
        $orderLineForWidgetInstance->billing_date = $this->cartProcessOptions->creation_date_override ?? date("Y-m-d H:i:s");

        // TODO - This will need to be updated when we have widget billing
        $orderLineForWidgetInstance->promo_price = 0;
        $orderLineForWidgetInstance->promo_fee = 0;
        $orderLineForWidgetInstance->price = 0;
        $orderLineForWidgetInstance->price_fee = 0;

        $orderLineForWidgetInstance->promo_duration = $currProduct->promo_cycle_duration;
        $orderLineForWidgetInstance->price_duration = $currProduct->value_duration;
        $orderLineForWidgetInstance->cycle_type = $currProduct->cycle_type;
        $orderLineForWidgetInstance->created_by = $this->defaultUserId;
        $orderLineForWidgetInstance->updated_by = $this->defaultUserId;

        $orderLineWidgetResult = $objOrderLines->createNew($orderLineForWidgetInstance);

        $cardAddon = new CardAddonModel();
        $cardAddon->company_id = $this->companyId;
        $cardAddon->division_id = 0;
        $cardAddon->user_id = $card->owner_id;
        $cardAddon->card_id = $card->card_id;
        $cardAddon->order_line_id = $orderLineWidgetResult->getData()->first()->order_line_id;
        $cardAddon->order_id = $orderLineWidgetResult->getData()->first()->order_id;
        $cardAddon->product_type_id = $currProduct->product_type_id;
        $cardAddon->product_id = $currProduct->product_id;
        $cardAddon->status = "active";
        $cardAddon->created_on = $this->cartProcessOptions->creation_date_override ?? date("Y-m-d");
        $cardAddon->last_updated = $this->cartProcessOptions->creation_date_override ?? date("Y-m-d");

        $cardAddon->widget_id = $currProduct->source_uuid;

        $cardAddonResult = (new CardAddon())->createNew($cardAddon);

        if ($cardAddonResult->result->Success === false)
        {
            return new ExcellTransaction(false);
        }

        $objAppInstanceRel = new AppInstanceRelModel();
        $objAppInstanceRel->app_instance_id = $appInstance->app_instance_id;
        $objAppInstanceRel->company_id = $this->companyId;
        $objAppInstanceRel->division_id = 0;
        $objAppInstanceRel->user_id = $card->owner_id;
        $objAppInstanceRel->module_app_widget_id = $moduleAppWidget->module_app_widget_id;
        $objAppInstanceRel->card_id = $card->card_id;
        $objAppInstanceRel->card_page_id = $objPage->page->card_tab_id;
        $objAppInstanceRel->card_page_rel_id = $objPage->pageRel->card_tab_rel_id;
        $objAppInstanceRel->card_addon_id = $cardAddonResult->getData()->first()->card_addon_id;
        $objAppInstanceRel->order_line_id = $orderLineWidgetResult->getData()->first()->order_line_id;
        $objAppInstanceRel->status = "active";
        $objAppInstanceRel->created_on = $this->cartProcessOptions->creation_date_override ?? date("Y-m-d");
        $objAppInstanceRel->last_updated = $this->cartProcessOptions->creation_date_override ?? date("Y-m-d");

        $appInstanceRelResult = (new AppInstanceRels())->getFks()->createNew($objAppInstanceRel);

        $moduleAppInstanceResult = (new ModuleApps())->createNewModuleAppInstance($objAppInstance->instance_uuid, $moduleApp->app_uuid, [
            "user_id" => $card->owner_id,
            "company_id" => $this->companyId,
            "division_id" => 0,
            "type_id" => 1,
        ]);

        if ($moduleAppInstanceResult->getResult()->Success === false) {
            return new ExcellTransaction(false, "There was an error", ["error"=> $moduleAppInstanceResult->getResult()->Message]);
        }

        $newPageNumber = $card->cardPageUsedCount + 1;
        $objPage->page->title = $currProduct->display_name . " " . $newPageNumber;
        $objPage->page->url = buildSnakeCaseFromPascalCase($currProduct->display_name) . "-" . $newPageNumber;
        $objPage->page->menu_title = $currProduct->display_name . " " . $newPageNumber;
        $objPage->page->library_tab = 1;
        $objPage->page->card_tab_type_id = 4;
        $objPage->page->created_on = $this->cartProcessOptions->creation_date_override ?? date("Y-m-d");
        $objPage->page->last_updated = $this->cartProcessOptions->creation_date_override ?? date("Y-m-d");
        (new CardPage())->update($objPage->page);

        $objPage->processed = true;

        $card->AddUnvalidatedValue("cardPageUsedCount", $newPageNumber);
        $card->cardPages->Add($objPage);

        return $appInstanceRelResult;
    }

    protected function installCardTemplate(CardModel &$card, $packageLine) : void
    {
        $defaultTemplateId = 30361;

        if ($this->productLineAttributes->FindEntityByValues(["package_line_id" => $packageLine->package_line_id, "label" => "default_template"]) !== null ) {
            $defaultTemplateId = $this->productLineAttributes->FindEntityByValues(["package_line_id" => $packageLine->package_line_id, "label" => "default_template"])->value;
        }

        if ($this->productLineAttributes->FindEntityByValues(["package_line_id" => $packageLine->package_line_id, "label" => "page_insertion_index"]) !== null) {
            $card->AddUnvalidatedValue("page_insertion_index", $this->productLineAttributes->FindEntityByValues(["package_line_id" => $packageLine->package_line_id, "label" => "page_insertion_index"])->value);
        } else {
            $card->AddUnvalidatedValue("page_insertion_index",1);
        }

        $objCards = new Cards();
        $objCards->CloneCardPages($defaultTemplateId, $card->card_id, false, false);
        $objCards->DeleteInvalidTemplatePages($defaultTemplateId, $card->card_id, $packageLine);
        $objCards->CloneCardConnections($defaultTemplateId, $card->card_id);
        $objCards->CloneCardPrimaryImage($defaultTemplateId, $card->card_id);
        $objCards->CloneCardSettings($defaultTemplateId, $card->card_id);
    }

    protected function registerCardPagesForMoving(CardModel &$card) : void
    {
        $currentCardPagesAfterIndexResult = (new CardPageRels())->getWhere([["card_id" => $card->card_id], "AND", ["rel_sort_order", ">=", $card->page_insertion_index ?? 1]], "rel_sort_order.ASC");
        $card->AddUnvalidatedValue("pagesToMove", $currentCardPagesAfterIndexResult->data);
    }

    // TODO - This productId is handed in via a hard-coded value: self::CardTypeId
    private function findProductCapsulesById() : ExcellCollection
    {
        $productTypesResult = (new ProductTypes())->getWhere(["product_primary", "!=", 0]);
        $productTypes = $productTypesResult->getData()->FieldsToArray(["product_type_id"]);

        $productCollection = new ExcellCollection();

        $this->loopThroughPackageLines(function(PackageLineModel $currPackageLine, PackageVariationModel $currPackage) use (&$productCollection, $productTypes)
        {
            if (empty($currPackageLine->entities) || !in_array($currPackageLine->product->product_type_id, $productTypes)) { return; }

            // this is always a card: self::CardTypeId
            // it loops through the results, but there is currently only always one.
            $currPackageLine->entities->Foreach(function(CartProductCapsule $cartProductCapsule) use (&$productCollection)
            {
                $cartProductCapsule->setProcessed(true);
                $productCollection->Add($cartProductCapsule);
                return $cartProductCapsule;
            });

            return $currPackageLine;
        });

        return $productCollection;
    }

    private function loopThroughPackageLines($callback) : void
    {
        $this->cartProcessTransaction->getPackages()->Foreach(function(PackageVariationModel $currPackage) use ($callback)
        {
            if (empty($currPackage->lines) || !is_a($currPackage->lines, ExcellCollection::class)) { return; }

            $currPackage->lines->Foreach(function(PackageLineModel $currPackageLine) use ($currPackage, $callback)
            {
                $result = $callback($currPackageLine, $currPackage);

                if ($result === null) { return; }

                return $result;
            });
        });
    }

    public function generateProductCreationErrors() : array
    {
        return [];
    }

    protected function getProductLineAttributes() : void
    {
        $productLineIds = [];

        $this->loopThroughPackageLines(function(PackageLineModel $currPackageLine, PackageVariationModel $currPackage) use (&$productLineIds)
        {
            $productLineIds[] = $currPackageLine->package_line_id;
        });

        $objPackageLineSettings = new PackageLineSettings();
        $this->productLineAttributes = $objPackageLineSettings->getWhereIn("package_line_id", $productLineIds)->getData();
    }

    public function getReferralCardNum() : string
    {
        if (empty($this->referralCard))
        {
            return "";
        }

        return $this->referralCard->card_num;
    }
}