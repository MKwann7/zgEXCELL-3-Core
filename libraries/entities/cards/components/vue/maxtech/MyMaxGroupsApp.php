<?php

namespace Entities\Cards\Components\Vue\Maxtech;

use Entities\Cards\Components\Vue\Maxtech\Groupwidget\ListMyGroupWidget;
use App\Website\Vue\Classes\VueApp;
use App\Website\Vue\Classes\VueModal;

class MyMaxGroupsApp extends VueApp
{
    protected string $appNamePlural = "My Groups";
    protected string $appNameSingular = "My Group";

    public function __construct($domId, ?VueModal &$modal = null)
    {
        $this->enableSlickSortContainerMixin();
        $this->enableSlickSortElementMixin();
        $this->enableSlickSortHandleDirective();

        $this->setDefaultComponentId(ListMyGroupWidget::getStaticId())
            ->setDefaultComponentAction("view");

        parent::__construct($domId, $modal);
    }
}