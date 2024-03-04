<?php

namespace Entities\Products\Components\Vue;

use App\Website\Vue\Classes\VueApp;
use Entities\Products\Components\Vue\ProductsWidget\ListMyProductsWidget;

class MyProductsApp extends VueApp
{
    protected string $appNamePlural = "My Products";
    protected string $appNameSingular = "My Product";

    public function __construct($domId, ?VueModal &$modal = null)
    {
        $this->enableSlickSortContainerMixin();
        $this->enableSlickSortElementMixin();
        $this->enableSlickSortHandleDirective();

        $this->setDefaultComponentId(ListMyProductsWidget::getStaticId())->setDefaultComponentAction("view");

        parent::__construct($domId, $modal);
    }

    public function renderAppData() : string
    {
        return "
        showNewSelection: true,
        ";
    }
}