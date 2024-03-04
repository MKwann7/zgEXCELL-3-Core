<?php

namespace Entities\Communication\Components\Vue;

use App\Website\Vue\Classes\VueApp;
use App\Website\Vue\Classes\VueModal;
use Entities\Communication\Components\Vue\CommunicationWidget\ShowCommunicationWidget;

class CommunicationMainApp extends VueApp
{
    protected string $appNamePlural = "Communication";
    protected string $appNameSingular = "Communication";

    public function __construct($domId, ?VueModal &$modal = null)
    {
        $this->enableSlickSortContainerMixin();
        $this->enableSlickSortElementMixin();
        $this->enableSlickSortHandleDirective();

        $this->setDefaultComponentId(ShowCommunicationWidget::getStaticId())->setDefaultComponentAction("view");

        parent::__construct($domId, $modal);
    }

    public function renderAppData() : string
    {
        return "
        showNewSelection: true,
        ";
    }
}