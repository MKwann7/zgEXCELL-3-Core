<?php

namespace Entities\Products\Components\Vue\ProductsWidget;

use App\Website\Constructs\Breadcrumb;
use App\Website\Vue\Classes\Base\VueComponent;
use App\Website\Vue\Classes\VueComponentEntityList;
use App\Website\Vue\Classes\VueProps;
use Entities\Cards\Components\Vue\CardWidget\ManageCardWidget;
use Entities\Cards\Models\CardAddonModel;

class ListProductsAdminWidget extends VueComponentEntityList
{
    protected $id = "5e7407ec-349f-4ec3-ad99-e51237a4830e";
    protected $title = "Products";
    protected $batchLoadEndpoint = "products/get-product-batches";
    protected $noEntitiesWarning = "There are no products to display.";

    public function __construct($defaultEntity = null, array $components = [])
    {
        $displayColumns = ["banner", "status"];

        $displayColumns = array_merge($displayColumns, ["card_name", "card_num", "card_vanity_url", "card_owner_name", "card_contacts", "product", "created_on", "last_updated"]);

        if ($defaultEntity === null)
        {
            $defaultEntity = (new CardAddonModel())
                ->setDefaultSortColumn("card_addon_id", "DESC")
                ->setDisplayColumns($this->buildDisplayColumns())
                ->setFilterColumns($this->buildFilterColumns())
                ->setRenderColumns($this->buildRenderColumns());
        }

        parent::__construct($defaultEntity, $components);

        $this->addFilterProps();

        $editorComponent = new ManageCardWidget();
        $editorComponent->addParentId($this->getInstanceId(), ["edit"]);

        $this->addComponentsList($editorComponent->getDynamicComponentsForParent());
        $this->addComponent($editorComponent);

        $this->modalTitleForAddEntity = "View Products";
        $this->modalTitleForEditEntity = "View Products";
        $this->modalTitleForDeleteEntity = "View Products";
        $this->modalTitleForRowEntity = "View Products";
        $this->setDefaultAction("view");
    }

    protected function addFilterProps(): void
    {
        $filterEntity = new VueProps("filterEntityId", "object", "filterEntityId");
        $filterByEntityValue = new VueProps("filterByEntityValue", "boolean", "filterByEntityValue");
        $filterByEntityRefresh = new VueProps("filterByEntityRefresh", "boolean", true);

        $this->addProp($filterEntity);
        $this->addProp($filterByEntityValue);
        $this->addProp($filterByEntityRefresh);
    }

    protected function buildDisplayColumns(): array
    {
        global $app;
        $displayColumns = ["avatar", "status"];

        if ($app->userAuthentication() && userCan("manage-platforms"))
        {
            $displayColumns[] = "platform";
        }

        $displayColumns = array_merge($displayColumns, ["user_id", "first_name", "last_name", "username"]);
        if ($this->showCards === true) { $displayColumns = array_merge($displayColumns, ["cards"]); }
        $displayColumns = array_merge($displayColumns, ["created_on", "last_updated"]);

        return $displayColumns;
    }

    protected function buildFilterColumns(): array
    {
        $filterColumns = ["user_id","first_name","last_name"];
        if ($this->showCards === true) { $filterColumns = array_merge($filterColumns, ["cards"]); }
        $filterColumns = array_merge($filterColumns, ["status"]);

        return $filterColumns;
    }

    protected function buildRenderColumns(): array
    {
        $filterColumns = ["avatar","status", "user_id", "first_name", "last_name", "username"];
        if ($this->showCards === true) { $filterColumns = array_merge($filterColumns, ["cards"]); }
        $filterColumns = array_merge($filterColumns, ["platform", "created_on", "last_updated", "sys_row_id"]);

        return $filterColumns;
    }

    protected function loadBreadCrumbs(): VueComponent
    {
        $this->addBreadcrumb(new Breadcrumb("Admin","/account/admin/", "link"));
        return $this;
    }
}