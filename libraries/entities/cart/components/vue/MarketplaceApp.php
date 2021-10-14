<?php

namespace Entities\Cart\Components\Vue;

use App\Website\Vue\Classes\VueApp;
use Entities\Cart\Components\Vue\MarketplaceWidget\MarketplaceWidget;

class MarketplaceApp extends VueApp
{
    protected $appNamePlural = "Marketplace";
    protected $appNameSingular = "Marketplace";

    public function __construct($domId, ?VueModal &$modal = null)
    {
        $this->enableSlickSortContainerMixin();
        $this->enableSlickSortElementMixin();
        $this->enableSlickSortHandleDirective();

        $this->setDefaultComponentId(MarketplaceWidget::getStaticId())->setDefaultComponentAction("view");

        parent::__construct($domId, $modal);
    }

    public function renderAppData() : string
    {
        return "
        showNewSelection: true,
        ";
    }
}