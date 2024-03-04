<?php

namespace Entities\Mobiniti\Classes;

use App\Core\AppEntity;
use Entities\Mobiniti\Models\MobinitiMessageModel;

class MobinitiMessages extends AppEntity
{
    public string $strEntityName       = "Mobiniti";
    public $strDatabaseTable    = "mobiniti_message";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = MobinitiMessageModel::class;
    public $strMainModelPrimary = "id";
}