<?php

namespace Entities\Cards\Components\Vue\Maxtech;

use App\Website\Vue\Classes\VueApp;
use App\Website\Vue\Classes\VueModal;
use Entities\Cards\Components\Vue\Maxtech\Sitewidget\ListMySiteWidget;

class MySiteMainApp extends VueApp
{
    protected string $appNamePlural = "My Max Sites";
    protected string $appNameSingular = "My Max Site";

    public function __construct($domId, ?VueModal &$modal = null)
    {
        $this->enableSlickSortContainerMixin();
        $this->enableSlickSortElementMixin();
        $this->enableSlickSortHandleDirective();

        parent::__construct($domId, $modal);
    }

    public function renderAppData() : string
    {
        return "
        showNewSelection: true,
        ";
    }
}