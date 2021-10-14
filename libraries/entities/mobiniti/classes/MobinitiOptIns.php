<?php

namespace Entities\Mobiniti\Classes;

use App\Core\AppEntity;
use Entities\Mobiniti\Models\MobinitiOptInModel;

class MobinitiOptIns extends AppEntity
{
    public $strEntityName       = "Mobiniti";
    public $strDatabaseTable    = "mobiniti_optin";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = MobinitiOptInModel::class;
    public $strMainModelPrimary = "id";
}