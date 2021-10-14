<?php

namespace Entities\Products\Classes;

use App\Utilities\Excell\ExcellCollection;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Cards\Classes\CardAddon;
use Entities\Cards\Classes\CardPage;
use Entities\Cards\Classes\CardPageRels;
use Entities\Modules\Classes\AppInstanceRels;
use Entities\Modules\Classes\AppInstances;
use Entities\Cards\Classes\Cards;
use Entities\Cards\Models\CardAddonModel;
use Entities\Cards\Models\CardModel;
use Entities\Cards\Models\CardPageModel;
use Entities\Cards\Models\CardPageRelModel;
use Entities\Cards\Models\AppInstancesModel;
use Entities\Cart\Classes\CartProcessTransaction;
use Entities\Cart\Classes\CartProductCapsule;
use Entities\Modules\Classes\ModuleApps;
use Entities\Modules\Models\AppInstanceModel;
use Entities\Modules\Models\AppInstanceRelModel;
use Entities\Packages\Classes\PackageLineSettings;
use Entities\Packages\Models\PackageLineModel;
use Entities\Packages\Models\PackageModel;
use Entities\Products\Models\ProductModel;
use Entities\Users\Classes\Users;
use Entities\Users\Models\UserModel;

class ProductProcessor
{
    /** @var CartProcessTransaction $cartProcessTransaction */
    public $cartProcessTransaction;
    public $user;
    public $companyId;
    public $defaultUserId;
    public $cartItems;
    public $productLineAttributes;
    /** @var $referralCard CardModel */
    private $referralCard;

    public const CardTypeId = 1;

    public function __construct (CartProcessTransaction $cartProcessTransaction)
    {
        $this->cartProcessTransaction = $cartProcessTransaction;
        $this->user = $this->getUserAccount($cartProcessTransaction->userId);
        $this->companyId = $cartProcessTransaction->companyId;
        $this->defaultUserId = $cartProcessTransaction->defaultUserId;
        $this->cartItems = new ExcellCollection();
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
        return (new Users())->getFks(["user_email","user_phone"])->getById($userId)->Data->First();
    }

    private function processCartParentItems() : void
    {
        // TODO - Change for ProductType....
        $colCardPackageLine = $this->findProductCapsulesById(self::CardTypeId);

        $colCardPackageLine->Foreach(function(CartProductCapsule $currProductCapsule)
        {
            $instantiatedProductResult = $this->processCartItemByProductId($currProductCapsule);

            if ($instantiatedProductResult->Result->Success === false) { return; }

            $currProductCapsule->setProductInstantiation($instantiatedProductResult->Data->First());

            $this->cartItems->Add($currProductCapsule);
        });
    }

    private function processCartItemByProductId(CartProductCapsule &$cartProductCapsule, ?CartProductCapsule &$cartParentProductCapsule = null, $hidden = false) : ExcellTransaction
    {
        $product = $cartProductCapsule->getProduct();

        switch($product->product_type_id)
        {
            case 1:
                $cardResult = $this->createNewCardFromOrder($cartProductCapsule);

                $card = $cardResult->Data->First();
                $this->installCardTemplate($card, $cartProductCapsule->getPackageLine());
                $this->registerCardPagesForMoving($card);

                $cartProductCapsule->setProcessed(true);
                return new ExcellTransaction(true, "We got it.", (new ExcellCollection())->Add($card));

            case 2:
                $cartProductCapsule->setProcessed(true);
                return $this->addDesignPackageToCard($cartProductCapsule, $cartParentProductCapsule->getProductInstantiation());

            case 3:
                $card = $cartParentProductCapsule->getProductInstantiation();
                $result = $this->addCardPageToCard($cartProductCapsule, $card, $hidden);
                $cartParentProductCapsule->setProductInstantiation($card);
                $cartProductCapsule->setProcessed(true);
                return $result;
        }

        return new ExcellTransaction(false);
    }

    private function processCartChildItemByProductId(CartProductCapsule &$cartProductCapsule, ?CartProductCapsule &$cartParentProductCapsule = null, $hidden = false) : ExcellTransaction
    {
        $product = $cartProductCapsule->getProduct();

        switch($product->product_type_id)
        {
            case 5:
                $card = $cartParentProductCapsule->getProductInstantiation();
                $result = $this->addWidgetToCard($cartProductCapsule, $card, $hidden);
                $cartParentProductCapsule->setProductInstantiation($card);
                $cartProductCapsule->setProcessed(true);
                return $result;
        }

        return new ExcellTransaction(false);
    }

    private function processChildItems() : void
    {
        $this->cartItems->Foreach(function(CartProductCapsule $currProductCapsule)
        {
            $colChildCartItemsFromParentId = $this->findChildItemsByCartItemId($currProductCapsule->cartItem, $currProductCapsule->getProductInstantiation());

            $colChildCartItemsFromParentId->Foreach(function(CartProductCapsule $currChildProductCapsule) use (&$currProductCapsule)
            {
                $this->processCartItemByProductId($currChildProductCapsule,$currProductCapsule);
                return $currChildProductCapsule;
            });

            return $this->processWidgetsAgainstCartCapsule($currProductCapsule);
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
        $colChildCartItemsFromParentId = $this->findChildItemsByCartItemId($currProductCapsule->cartItem, $currProductCapsule->getProductInstantiation());

        $colChildCartItemsFromParentId->Foreach(function(CartProductCapsule $currChildProductCapsule) use (&$currProductCapsule, $hidden)
        {
            $this->processCartChildItemByProductId($currChildProductCapsule, $currProductCapsule, $hidden);

            return $currChildProductCapsule;
        });

        $this->movePagesAfterIndex($currProductCapsule->getProductInstantiation());

        return $currProductCapsule;
    }

    private function movePagesAfterIndex(CardModel $card) : void
    {
        if (empty($card->pagesToMove) || $card->pagesToMove->Count() === 0)
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

    private function findChildItemsByCartItemId($cartItem = null, $productInstantiation) : ExcellCollection
    {
        // This will eventually match up the cart items by the cart item id (cartItem->cart_item_id).
        // Right now, we are only getting the items that were not just processed (the only card in the cart),
        // And attached them to a collection.
        $colChildItems = new ExcellCollection();

        $this->loopThroughPackageLines(function(PackageLineModel $currPackageLine, PackageModel $currPackage) use (&$colChildItems, $productInstantiation)
        {
            $currPackageLine->entities->Foreach(function(CartProductCapsule $cartProductCapsule) use (&$colChildItems, $productInstantiation)
            {
                if ($cartProductCapsule->processed === true) { return; }

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
        $orderLine = $cartProductCapsule->getOrderLine();

        $orderLineId = $orderLine->order_line_id;

        $cards = new Cards();
        $objCardNumCheck = $cards->getWhere(null, "card_num.DESC", 1)->Data->First();
        $intNewCardNum = $objCardNumCheck->card_num + 1;

        $objCardCreate = new CardModel();
        $objCardCreate->owner_id = $this->user->user_id;
        $objCardCreate->card_user_id = $this->user->user_id;
        $objCardCreate->company_id = $this->companyId;
        $objCardCreate->division_id = 0;
        $objCardCreate->card_name = "Card for {$this->user->first_name} {$this->user->last_name} - {$this->user->user_id}";
        $objCardCreate->product_id = $product->product_id;
        $objCardCreate->status = "Build";
        $objCardCreate->template_id = 1;
        $objCardCreate->template_card = ExcellFalse;
        $objCardCreate->card_type_id = 1;
        $objCardCreate->card_num = $intNewCardNum;
        $objCardCreate->created_by = $this->defaultUserId;
        $objCardCreate->updated_by = $this->defaultUserId;
        $objCardCreate->created_on = date("Y-m-d H:i:s");
        $objCardCreate->last_updated = date("Y-m-d H:i:s");
        $objCardCreate->order_line_id = $orderLineId;

        return $cards->createNew($objCardCreate);
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

        return $objCardAddon->createNew($cardAddon);
    }

    protected function addCardPageToCard(CartProductCapsule $cartProductCapsule, CardModel &$card, $hidden = false) : ExcellTransaction
    {
        $currProduct = $cartProductCapsule->getProduct();
        $orderLine = $cartProductCapsule->getOrderLine();
        $packageLineId = $cartProductCapsule->getPackageLine()->package_line_id;

        if (empty($card->cardPages)) { $card->AddUnvalidatedValue("cardPages", new ExcellCollection()); }

        $cardPageIndex = $card->page_insertion_index + $card->cardPages->Count();
        $newCardPage = new \stdClass();
        $objCardPage = new CardPageModel();
        $objCardPage->user_id = $card->owner_id;
        $objCardPage->company_id = $this->companyId;
        $objCardPage->division_id = 0;
        $objCardPage->card_tab_type_id = 1; // Defaulting to HTML page
        $objCardPage->title = "Untitled Page";
        $objCardPage->library_tab = ExcellFalse;
        $objCardPage->permanent = ExcellFalse;
        $objCardPage->order_number = $cardPageIndex;
        $objCardPage->visibility = ($hidden === false ? ExcellTrue : ExcellFalse );
        $objCardPage->created_by = $this->defaultUserId;
        $objCardPage->updated_by = $this->defaultUserId;

        $pageContent = "";

        if ($this->productLineAttributes->FindEntityByValues(["package_line_id" => $packageLineId, "label" => "page_content"]) !== null)
        {
            $pageContent = base64_encode($this->productLineAttributes->FindEntityByValues(["package_line_id" => $packageLineId, "label" => "page_content"])->value);
        }

        $objCardPage->content = $pageContent;

        $objNewCardPageResult = (new CardPage())->getFks()->createNew($objCardPage);

        $newCardPage->page = $objNewCardPageResult->Data->First();
        $newCardPage->card_tab_id = $objNewCardPageResult->Data->First()->card_tab_id;
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

        $newCardPage->cardAddon = $objCardAddon->createNew($cardAddon)->Data->First();

        $objCardPageRelResult = new CardPageRelModel();
        $objCardPageRelResult->card_tab_id = $newCardPage->page->card_tab_id;
        $objCardPageRelResult->card_id = $card->card_id;
        $objCardPageRelResult->user_id = $card->owner_id;
        $objCardPageRelResult->rel_sort_order = $cardPageIndex;
        $objCardPageRelResult->rel_visibility = ($hidden === false ? ExcellTrue : ExcellFalse );
        $objCardPageRelResult->card_tab_rel_type = "default";
        $objCardPageRelResult->card_addon_id = $cardAddon->card_addon_id;
        $objCardPageRelResult->order_line_id =  $orderLine->order_line_id;

        $objNewCardPageRelResult = (new CardPageRels())->getFks()->createNew($objCardPageRelResult);
        $newCardPage->pageRel = $objNewCardPageRelResult->Data->First();


        $newCardPage->processed = false;

        $card->cardPages->Add($newCardPage);

        return new ExcellTransaction(true, "Page Created", (new ExcellCollection())->Add($newCardPage));
    }

    protected function createNewCardPageForWidget(&$card, CartProductCapsule &$cartProductCapsule, $hidden = false) : ExcellTransaction
    {
        $product = new ProductModel();
        $product->product_id = 1003;
        $product->product_type_id = 3;
        $product->AddUnvalidatedValue("orderLine", $cartProductCapsule->getOrderLine());

        $cartProductCapsole = new CartProductCapsule();
        $cartProductCapsole->setProduct($product);
        $cartProductCapsole->setOrderLine($cartProductCapsule->getOrderLine());
        $cartProductCapsole->setPackageLine($cartProductCapsule->getPackageLine());

        return $this->addCardPageToCard($cartProductCapsole,$card, $hidden);
    }

    protected function addWidgetToCard(CartProductCapsule &$cartProductCapsule, CardModel &$card, $hidden = false) : ExcellTransaction
    {
        $currProduct = $cartProductCapsule->getProduct();
        $orderLine = $cartProductCapsule->getOrderLine();

        $objPageResult = $this->createNewCardPageForWidget($card, $cartProductCapsule, $hidden);

        if ($objPageResult->Result->Success === false)
        {
            return new ExcellTransaction(false);
        }

        $objPage = $objPageResult->Data->First();

        if (empty($card->cardPageUsedCount)) { $card->AddUnvalidatedValue("cardPageUsedCount", 0); }

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

        $cardAddon->widget_id = $currProduct->source_uuid;

        $cardAddonResult = (new CardAddon())->createNew($cardAddon);

        if ($cardAddonResult->Result->Success === false)
        {
            return new ExcellTransaction(false);
        }

        $cardAddon = $cardAddonResult->Data->First();

        $objModuleApps = new ModuleApps();
        $moduleAppResult = $objModuleApps->getLatestModuleWidgetsByUuid($currProduct->source_uuid);

        $moduleApp = $moduleAppResult->Data->First();
        $moduleAppWidget = $moduleApp->widgets->FindEntityByValue("widget_class", 1004);

        $objAppInstance = new AppInstanceModel();
        $objAppInstance->owner_id = $card->owner_id;
        $objAppInstance->card_id = $card->card_id;
        $objAppInstance->card_tab_id = $objPage->page->card_tab_id;
        $objAppInstance->card_addon_id = $cardAddon->card_addon_id;
        $objAppInstance->module_app_id = $moduleApp->module_app_id;
        $objAppInstance->module_app_widget_id = $moduleAppWidget->module_app_widget_id;
        $objAppInstance->product_id = $currProduct->product_id;
        $objAppInstance->instance_uuid = getGuid();

        $appInstanceResult = (new AppInstances())->getFks()->createNew($objAppInstance);

        $appInstance = $appInstanceResult->Data->First();

        $objAppInstanceRel = new AppInstanceRelModel();
        $objAppInstanceRel->app_instance_id = $appInstance->app_instance_id;
        $objAppInstanceRel->company_id = $this->companyId;
        $objAppInstanceRel->division_id = 0;
        $objAppInstanceRel->user_id = $card->owner_id;
        $objAppInstanceRel->card_id = $card->card_id;
        $objAppInstanceRel->card_page_id = $objPage->page->card_tab_id;
        $objAppInstanceRel->card_page_rel_id = $objPage->pageRel->card_tab_rel_id;
        $objAppInstanceRel->card_addon_id = $cardAddon->card_addon_id;
        $objAppInstanceRel->order_line_id = $orderLine->order_line_id;

        $appInstanceRelResult = (new AppInstanceRels())->getFks()->createNew($objAppInstanceRel);

        $moduleAppInstanceResult = (new ModuleApps())->createNewModuleAppInstance($objAppInstance->instance_uuid, $moduleApp->app_uuid);

        $objPage->page->title = "Directory " . ($card->cardPageUsedCount + 1);
        $objPage->page->library_tab = 1;
        $objPage->page->card_tab_type_id = 4;
        (new CardPage())->update($objPage->page);

        $objPage->processed = true;

        $card->AddUnvalidatedValue("cardPageUsedCount", ($card->cardPageUsedCount + 1));
        $card->cardPages->Add($objPage);

        return $appInstanceRelResult;
    }

    protected function installCardTemplate(CardModel &$card, $packageLine) : void
    {
        $defaultTemplateId = 30361;

        if ($this->productLineAttributes->FindEntityByValues(["package_line_id" => $packageLine->package_line_id, "label" => "default_template"]) !== null)
        {
            $defaultTemplateId = $this->productLineAttributes->FindEntityByValues(["package_line_id" => $packageLine->package_line_id, "label" => "default_template"])->value;
        }

        if ($this->productLineAttributes->FindEntityByValues(["package_line_id" => $packageLine->package_line_id, "label" => "page_insertion_index"]) !== null)
        {
            $card->AddUnvalidatedValue("page_insertion_index", $this->productLineAttributes->FindEntityByValues(["package_line_id" => $packageLine->package_line_id, "label" => "page_insertion_index"])->value);
        }
        else
        {
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
        $card->AddUnvalidatedValue("pagesToMove", $currentCardPagesAfterIndexResult->Data);
    }

    private function findProductCapsulesById($productId) : ExcellCollection
    {
        $productCollection = new ExcellCollection();

        $this->loopThroughPackageLines(function(PackageLineModel $currPackageLine, PackageModel $currPackage) use (&$productCollection, $productId)
        {
            if (empty($currPackageLine->entities) || $currPackageLine->product->product_type_id !== $productId) { return; }

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
        $this->cartProcessTransaction->getPackages()->Foreach(function(PackageModel $currPackage) use ($callback)
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

        $this->loopThroughPackageLines(function(PackageLineModel $currPackageLine, PackageModel $currPackage) use (&$productLineIds)
        {
            $productLineIds[] = $currPackageLine->package_line_id;
        });

        $objPackageLineSettings = new PackageLineSettings();
        $this->productLineAttributes = $objPackageLineSettings->getWhereIn("package_line_id", $productLineIds)->Data;
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