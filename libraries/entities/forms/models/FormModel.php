<?php

namespace Entities\Forms\Models;

use App\Core\AppModel;

class FormModel extends AppModel
{
    protected $EntityName = "Forms";
    protected $ModelName = "Form";

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