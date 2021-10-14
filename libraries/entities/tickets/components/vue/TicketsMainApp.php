<?php

namespace Entities\Tickets\Components\Vue;

use App\Website\Vue\Classes\VueApp;
use App\Website\Vue\Classes\VueModal;
use Entities\Tickets\Components\Vue\TicketsWidget\ListTicketsWidget;

class TicketsMainApp extends TicketsAdminApp
{
    protected $appNamePlural = "My Queue";
    protected $appNameSingular = "My Queue";

    public function __construct($domId, ?VueModal &$modal = null)
    {
        parent::__construct($domId, $modal);

        $this->setDefaultComponentId(ListTicketsWidget::getStaticId())->setDefaultComponentAction("view");
    }

    public function renderAppData() : string
    {
        return "
        showNewSelection: true,
        ";
    }
}