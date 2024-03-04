<?php

namespace Entities\Tickets\Components\Vue;

use App\Website\Vue\Classes\VueApp;
use App\Website\Vue\Classes\VueModal;
use Entities\Tickets\Components\Vue\TicketsWidget\ListTicketsAdminWidget;

class TicketsAdminApp extends VueApp
{
    protected string $appNamePlural = "Tickets";
    protected string $appNameSingular = "Ticket";

    public function __construct($domId, ?VueModal &$modal = null)
    {
        $this->enableSlickSortContainerMixin();
        $this->enableSlickSortElementMixin();
        $this->enableSlickSortHandleDirective();

        $this->setDefaultComponentId(ListTicketsAdminWidget::getStaticId())->setDefaultComponentAction("view");

        parent::__construct($domId, $modal);
    }

    public function renderAppData() : string
    {
        return "
        showNewSelection: true,
        ";
    }
}