<?php

namespace Entities\Groups\Models;

use App\Core\AppModel;

class GroupModel extends AppModel
{
    protected string $EntityName = "Groups";
    protected string $ModelName = "Group";

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