<?php

namespace Entities\Visitors\Classes;

use App\Core\AppController;
use App\Core\AppEntity;
use Entities\Visitors\Models\VisitorModel;

class Visitors extends AppEntity
{
    public string $strEntityName       = "Visitors";
    public $strDatabaseTable    = "visitor_activity";
    public $strDatabaseName     = "Traffic";
    public $strMainModelName    = VisitorModel::class;
    public $strMainModelPrimary = "visitor_activity_id";
    public $isPrimaryModule     = true;
}
