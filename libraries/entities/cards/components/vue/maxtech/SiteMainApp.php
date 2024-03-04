<?php

namespace Entities\Cards\Components\Vue\Maxtech;

use App\Website\Vue\Classes\VueApp;
use App\Website\Vue\Classes\VueModal;
use Entities\Cards\Components\Vue\Maxtech\Sitewidget\ListSiteWidget;

class SiteMainApp extends VueApp
{
    protected string $appNamePlural = "Max Sites";
    protected string $appNameSingular = "Max Site";

    public function __construct($domId, ?VueModal &$modal = null)
    {
        $this->enableSlickSortContainerMixin();
        $this->enableSlickSortElementMixin();
        $this->enableSlickSortHandleDirective();

        $this->setDefaultComponentId(ListSiteWidget::getStaticId())->setDefaultComponentAction("view");

        parent::__construct($domId, $modal);
    }

    public function renderAppData() : string
    {
        return "
        showNewSelection: true,
        ";
    }
}