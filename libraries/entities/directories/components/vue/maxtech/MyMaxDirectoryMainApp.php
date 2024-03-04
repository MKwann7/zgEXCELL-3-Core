<?php

namespace Entities\Directories\Components\Vue\Maxtech;

use App\Website\Vue\Classes\VueApp;
use App\Website\Vue\Classes\VueModal;
use Entities\Directories\Components\Vue\Maxtech\Directorywidget\ListMaxDirectoryWidget;

class MyMaxDirectoryMainApp extends VueApp
{
    protected string $appNamePlural = "Max Directories";
    protected string $appNameSingular = "Max Directory";

    public function __construct($domId, ?VueModal &$modal = null)
    {
        $this->enableSlickSortContainerMixin();
        $this->enableSlickSortElementMixin();
        $this->enableSlickSortHandleDirective();

        $this->setDefaultComponentId(ListMaxDirectoryWidget::getStaticId())->setDefaultComponentAction("view");

        parent::__construct($domId, $modal);
    }

    public function renderAppData() : string
    {
        return "
        showNewSelection: true,
        ";
    }
}