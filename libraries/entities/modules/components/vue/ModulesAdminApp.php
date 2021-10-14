<?php

namespace Entities\Modules\Components\Vue;

use App\Website\Vue\Classes\VueApp;
use App\Website\Vue\Classes\VueModal;
use Entities\Modules\Components\Vue\AppsWidget\ListModuleAppsAdminWidget;

class ModulesAdminApp extends VueApp
{
    protected $appNamePlural = "Modules";
    protected $appNameSingular = "Module";

    public function __construct($domId, ?VueModal &$modal = null)
    {
        $this->enableSlickSortContainerMixin();
        $this->enableSlickSortElementMixin();
        $this->enableSlickSortHandleDirective();

        $this->setDefaultComponentId(ListModuleAppsAdminWidget::getStaticId())->setDefaultComponentAction("view");

        parent::__construct($domId, $modal);
    }

    public function renderAppData() : string
    {
        return "
        showNewSelection: true,
        ";
    }
}