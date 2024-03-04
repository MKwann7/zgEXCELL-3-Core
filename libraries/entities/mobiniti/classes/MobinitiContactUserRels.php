<?php

namespace Entities\Mobiniti\Classes;

use App\Core\AppEntity;
use Entities\Mobiniti\Models\MobinitiContactUserRelModel;

class MobinitiContactUserRels extends AppEntity
{
    public string $strEntityName       = "Mobiniti";
    public $strDatabaseTable    = "mobiniti_contact_user_rel";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = MobinitiContactUserRelModel::class;
    public $strMainModelPrimary = "mobiniti_contact_user_rel_id";
}