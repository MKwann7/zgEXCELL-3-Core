<?php

namespace Entities\Reports\Models;

use App\Core\AppModel;

class ReportModel extends AppModel
{
    protected $EntityName = "Reports";
    protected $ModelName = "Report";

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