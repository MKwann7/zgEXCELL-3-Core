<?php

namespace Entities\Tickets\Components\Vue\TicketsWidget;

use App\Website\Constructs\Breadcrumb;
use App\Website\Vue\Classes\VueComponentEntityList;
use Entities\Tickets\Models\TicketModel;

class ListTicketsWidget extends ListTicketsAdminWidget
{
    protected $id = "712bc155-26a8-4a6a-b972-49aeadcb8741";
    protected $title = "My Queue";
    protected $queueFilterCacheId = "my-ticket-queue-filter-id";

    protected function renderComponentHydrationScript() : string
    {
        global $app;
        return parent::renderComponentHydrationScript() . '
            this.filterEntityId = '.$app->getActiveLoggedInUser()->user_id.'
        ';
    }
}