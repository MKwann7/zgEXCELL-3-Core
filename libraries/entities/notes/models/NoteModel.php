<?php

namespace Entities\Notes\Models;

use App\Core\AppModel;

class NoteModel extends AppModel
{
    protected $EntityName = "Notes";
    protected $ModelName = "Note";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }
    private function loadDefinitions()
    {
        return [
            "note_id" => ["type" => "int","length" => 15],
            "company_id" => ["type" => "int","length" => 15],
            "division_id" => ["type" => "int","length" => 15],
            "entity_id" => ["type" => "int","length" => 15],
            "entity_name" => ["type" => "varchar","length" => 15],
            "note_owner_id" => ["type" => "int","length" => 15],
            "ticket_id" => ["type" => "int","length" => 15],
            "summary" => ["type" => "varchar","length" => 75],
            "description" => ["type" => "varchar","length" => 500],
            "visibility" => ["type" => "varchar","length" => 25],
            "type" => ["type" => "varchar","length" => 25],
            "created_on" => ["type" => "datetime"],
            "last_updated" => ["type" => "datetime"],
            "sys_row_id" => ["type" => "char","length" => 36,"nullable" => true]
        ];
    }
}