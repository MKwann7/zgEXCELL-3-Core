<?php

namespace Entities\Cards\Components\Vue\Maxtech;

use Entities\Cards\Components\Vue\Maxtech\Groupwidget\ListMaxGroupWidget;
use App\Website\Vue\Classes\VueApp;
use App\Website\Vue\Classes\VueModal;

class MaxGroupsApp extends VueApp
{
    protected string $appNamePlural = "Max Groups";
    protected string $appNameSingular = "Max Group";

    public function __construct($domId, ?VueModal &$modal = null)
    {
        $this->enableSlickSortContainerMixin();
        $this->enableSlickSortElementMixin();
        $this->enableSlickSortHandleDirective();

        $this->setDefaultComponentId(ListMaxGroupWidget::getStaticId())
            ->setDefaultComponentAction("view");

        parent::__construct($domId, $modal);
    }
}