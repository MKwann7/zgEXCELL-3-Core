<?php

namespace Entities\Opportunity\Classes;

use App\Core\AppEntity;
use Entities\Opportunity\Models\OpportunityLineModel;

class OpportunityLine extends AppEntity
{
    public string $strEntityName       = "Opportunity";
    public $strDatabaseTable    = "opportunity_line";
    public $strDatabaseName     = "Crm";
    public $strMainModelName    = OpportunityLineModel::class;
    public $strMainModelPrimary = "opportunity_line_id";

    public function GetAllActiveOpportunityLines()
    {
        return $this->getFks()->getWhere("status","=","Active");
    }
}