<?php

namespace Entities\Opportunity\Classes;

use App\Core\AppEntity;
use Entities\Opportunity\Models\OpportunityModel;

class Opportunity extends AppEntity
{
    public $strEntityName       = "Opportunity";
    public $strDatabaseTable    = "opportunity";
    public $strDatabaseName     = "Crm";
    public $strMainModelName    = OpportunityModel::class;
    public $strMainModelPrimary = "opportunity_id";

    public function GetAllActiveOpportunities()
    {
        return $this->getFks()->getWhere("status","=","Active");
    }
}