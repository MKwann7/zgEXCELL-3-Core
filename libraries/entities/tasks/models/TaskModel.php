<?php

namespace Entities\Tasks\Models;

use App\Core\AppModel;

class TaskModel extends AppModel
{
    protected string $EntityName = "Tasks";
    protected string $ModelName = "Task";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions(): array
    {
        return [];
    }
}