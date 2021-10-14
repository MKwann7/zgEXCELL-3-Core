<?php

namespace Entities\Companies\Components\Vue;

use App\Website\Vue\Classes\VueApp;
use App\Website\Vue\Classes\VueModal;
use Entities\Companies\Components\Vue\PlatformWidget\ListPlatformWidget;

class PlatformMainApp extends VueApp
{
    protected $appNamePlural = "Custom Platforms";
    protected $appNameSingular = "Custom Platform";

    public function __construct($domId, ?VueModal &$modal = null, $pageRequest = [])
    {
        $this->enableSlickSortContainerMixin();
        $this->enableSlickSortElementMixin();
        $this->enableSlickSortHandleDirective();

        $this->setDefaultComponentId(ListPlatformWidget::getStaticId())->setDefaultComponentAction("view");

        parent::__construct($domId, $modal);
    }
}