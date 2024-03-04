<?php

namespace Entities\Users\Classes;

use App\Core\AppEntity;
use Entities\Users\Models\ConnectionTypeModel;

class ConnectionTypes extends AppEntity
{
    public string $strEntityName       = "Users";
    public $strDatabaseTable    = "connection_type";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = ConnectionTypeModel::class;
    public $strMainModelPrimary = "connection_type_id";
}