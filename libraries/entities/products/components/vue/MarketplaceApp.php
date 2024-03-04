<?php

namespace Entities\Products\Components\Vue;

use Entities\Products\Components\Vue\MarketplaceWidget\ListMarketplaceProductsWidget;

class MarketplaceApp
{
    protected string $appNamePlural = "Marketplace";
    protected string $appNameSingular = "Marketplace";

    public function __construct($domId, ?VueModal &$modal = null)
    {
        $this->enableSlickSortContainerMixin();
        $this->enableSlickSortElementMixin();
        $this->enableSlickSortHandleDirective();

        $this->setDefaultComponentId(ListMarketplaceProductsWidget::getStaticId())->setDefaultComponentAction("view");

        parent::__construct($domId, $modal);
    }

    public function renderAppData() : string
    {
        return "
        showNewSelection: true,
        ";
    }
}