<?php

namespace Entities\Cards\Components\Vue;

use App\Website\Vue\Classes\VueApp;
use App\Website\Vue\Classes\VueModal;
use Entities\Cards\Components\Vue\CardPageWidget\ListCardPageWidget;

class CardWidgetLibraryApp extends VueApp
{
    protected $appTitle = "Widget Library";
    protected string $appNamePlural = "Card Widgets";
    protected string $appNameSingular = "Card Widget";

    public function __construct($domId, ?VueModal &$modal = null)
    {
        $this->enableSlickSortContainerMixin();
        $this->enableSlickSortElementMixin();
        $this->enableSlickSortHandleDirective();

        $this->setDefaultComponentId(ListCardPageWidget::getStaticId())->setDefaultComponentAction("view");

        parent::__construct($domId, $modal);
    }
}