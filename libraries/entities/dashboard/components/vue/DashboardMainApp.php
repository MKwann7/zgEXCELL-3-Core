<?php

namespace Entities\Dashboard\Components\Vue;

use App\Website\Vue\Classes\VueApp;
use App\Website\Vue\Classes\VueModal;
use Entities\Dashboard\Components\Vue\DashboardWidget\DashboardWidget;

class DashboardMainApp extends VueApp
{
    protected $appNamePlural = "Dashboard";
    protected $appNameSingular = "Dashboard";

    public function __construct($domId, ?VueModal &$modal = null)
    {
        $this->enableSlickSortContainerMixin();
        $this->enableSlickSortElementMixin();
        $this->enableSlickSortHandleDirective();

        $this->setDefaultComponentId(DashboardWidget::getStaticId())->setDefaultComponentAction("view");

        parent::__construct($domId, $modal);
    }

    public function renderAppData() : string
    {
        return "
        showNewSelection: true,
        ";
    }
}