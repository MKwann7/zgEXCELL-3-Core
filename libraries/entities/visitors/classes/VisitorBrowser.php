<?php

namespace Entities\Visitors\Classes;

use App\Core\AppController;
use App\Core\AppEntity;
use Entities\Visitors\Models\VisitorBrowserModel;

class VisitorBrowser extends AppEntity
{
    public $strEntityName       = "Visitors";
    public $strDatabaseTable    = "visitor_browser";
    public $strDatabaseName     = "Traffic";
    public $strMainModelName    = VisitorBrowserModel::class;
    public $strMainModelPrimary = "visitor_browser_id";
}