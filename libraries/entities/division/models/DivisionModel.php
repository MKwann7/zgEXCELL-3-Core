<?php

namespace Entities\Division\Models;

use App\Core\AppModel;

class DivisionModel extends AppModel
{
    protected string $EntityName = "Division";
    protected string $ModelName = "Division";

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