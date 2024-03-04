<?php

namespace Entities\Users\Classes;

use App\Core\AppEntity;
use Entities\Users\Models\ConnectionRelModel;

class ConnectionRels extends AppEntity
{
    public string $strEntityName       = "Users";
    public $strDatabaseTable    = "connection_rel";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = ConnectionRelModel::class;
    public $strMainModelPrimary = "connection_rel_id";
}