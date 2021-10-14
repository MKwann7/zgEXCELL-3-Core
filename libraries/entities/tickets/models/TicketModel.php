<?php

namespace Entities\Tickets\Models;

use App\Core\AppModel;

class TicketModel extends AppModel
{
    protected $EntityName = "Tickets";
    protected $ModelName = "Ticket";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "ticket_id" => ["type" => "int","length" => 15],
            "company_id" => ["type" => "int","length" => 15],
            "division_id" => ["type" => "int","length" => 15],
            "parent_ticket_id" => ["type" => "int","length" => 15],
            "owner_id" => ["type" => "int","length" => 15],
            "summary" => ["type" => "varchar","length" => 75],
            "description" => ["type" => "varchar","length" => 500],
            "status" => ["type" => "varchar","length" => 25],
            "entity_id" => ["type" => "int","length" => 15],
            "entity_name" => ["type" => "varchar","length" => 15],
            "ticket_queue_id" => ["type" => "int","length" => 15],
            "journey_id" => ["type" => "int","length" => 15],
            "type" => ["type" => "varchar","length" => 25],
            "ticket_opened" => ["type" => "datetime"],
            "expected_completion" => ["type" => "datetime"],
            "ticket_closed" => ["type" => "datetime"],
            "duration" => ["type" => "int","length" => 15],
            "created_on" => ["type" => "datetime"],
            "last_updated" => ["type" => "datetime"],
            "sys_row_id" => ["type" => "char","length" => 36,"nullable" => true]
        ];
    }

    public function LoadAddons($addon) : void
    {

    }
}