<?php

namespace Entities\Mobiniti\Classes;

use App\Core\AppEntity;
use Entities\Mobiniti\Models\MobinitiModel;

class Mobiniti extends AppEntity
{
    public $strEntityName       = "Mobiniti";
    public $strDatabaseTable    = "mobiniti_main";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = MobinitiModel::class;
    public $strMainModelPrimary = "id";
}
