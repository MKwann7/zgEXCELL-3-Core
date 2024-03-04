<?php

namespace Entities\Mobiniti\Classes;

use App\Core\AppEntity;
use Entities\Mobiniti\Models\MobinitiContactGroupRelModel;

class MobinitiContactGroupRels extends AppEntity
{
    public string $strEntityName       = "Mobiniti";
    public $strDatabaseTable    = "mobiniti_contact_group_rel";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = MobinitiContactGroupRelModel::class;
    public $strMainModelPrimary = "mobiniti_contact_group_rel_id";
}