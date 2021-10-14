<?php

namespace Entities\Security\Models;

use App\Core\AppModel;

class SecurityModel extends AppModel
{
    protected $EntityName = "Security";
    protected $ModelName = "Security";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [];
    }
}