<?php

namespace Entities\Products\Components\Vue;

use App\Website\Vue\Classes\VueApp;

class ProductsApp extends VueApp
{
    protected string $appNamePlural = "Products";
    protected string $appNameSingular = "Product";

    public function __construct($domId, ?VueModal &$modal = null)
    {
        $this->enableSlickSortContainerMixin();
        $this->enableSlickSortElementMixin();
        $this->enableSlickSortHandleDirective();

        $this->setDefaultComponentId(ListProductsAdminWidget::getStaticId())->setDefaultComponentAction("view");

        parent::__construct($domId, $modal);
    }

    public function renderAppData() : string
    {
        return "
        showNewSelection: true,
        ";
    }
}