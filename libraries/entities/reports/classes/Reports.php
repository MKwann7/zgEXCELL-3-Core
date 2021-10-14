<?php

namespace Entities\Reports\Classes;

use App\Core\AppEntity;
use Entities\Reports\Models\ReportModel;

class Reports extends AppEntity
{
    public $strEntityName       = "reports";
    public $strDatabaseTable    = "report";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = ReportModel::class;
    public $strMainModelPrimary = "report_id";
    public $isPrimaryModule     = true;
}
