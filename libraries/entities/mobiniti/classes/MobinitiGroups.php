<?php

namespace Entities\Mobiniti\Classes;

use Entities\Mobiniti\Models\MobinitiGroupModel;

class MobinitiGroups extends Mobiniti
{
    public $strEntityName       = "Mobiniti";
    public $strDatabaseTable    = "mobiniti_group";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = MobinitiGroupModel::class;
    public $strMainModelPrimary = "id";
}