<?php

namespace Entities\Packages\Components\Vue;

use App\Website\Vue\Classes\VueApp;
use App\Website\Vue\Classes\VueModal;
use Entities\Packages\Components\Vue\PackageWidget\ListPackageWidget;

class PackageMainApp extends VueApp
{
    protected string $appNamePlural = "Packages";
    protected string $appNameSingular = "Packages";

    public function __construct($domId, ?VueModal &$modal = null, $pageRequest = [])
    {
        $this->enableSlickSortContainerMixin();
        $this->enableSlickSortElementMixin();
        $this->enableSlickSortHandleDirective();

        $this->setDefaultComponentId(ListPackageWidget::getStaticId())->setDefaultComponentAction("view");

        parent::__construct($domId, $modal);
    }
}