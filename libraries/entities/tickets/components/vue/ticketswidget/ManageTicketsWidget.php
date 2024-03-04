<?php

namespace Entities\Tickets\Components\Vue\TicketsWidget;

class ManageTicketsWidget extends ManageTicketsAdminWidget
{
    protected string $id = "7f4d81e1-db9e-4f0b-a486-bbbf26097568";
    protected string $title = "My Ticket Dashboard";
    protected string $endpointUriAbstract = "ticket-dashboard/{id}";

    public function __construct(array $components = [])
    {
        parent::__construct();
    }

}