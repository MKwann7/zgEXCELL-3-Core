<?php

namespace Entities\Tickets\Models;

use App\Core\AppModel;

class JourneyModel extends AppModel
{
    protected $EntityName = "Journeys";
    protected $ModelName = "Journey";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "journey_id" => ["type" => "int","length" => 15],
            "company_id" => ["type" => "int","length" => 15],
            "division_id" => ["type" => "int","length" => 15],
            "parent_id" => ["type" => "int","length" => 15],
            "follows_id" => ["type" => "int","length" => 15],
            "delay_days" => ["type" => "int","length" => 5],
            "journey_type_id" => ["type" => "int","length" => 15],
            "ticket_queue_id" => ["type" => "int","length" => 15],
            "label" => ["type" => "varchar","length" => 75],
            "name" => ["type" => "varchar","length" => 75],
            "description" => ["type" => "varchar","length" => 750],
            "expected_duration" => ["type" => "int","length" => 15],
            "hierarchical_progression" => ["type" => "boolean"],
            "sys_row_id" => ["type" => "uuid"]
        ];
    }

    public function LoadAddons($addon) : void
    {

    }
}