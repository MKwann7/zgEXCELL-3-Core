<?php

namespace Entities\Mobiniti\Classes;

use App\Core\AppEntity;
use Entities\Mobiniti\Models\MobinitiCampaignModel;

class MobinitiCampaigns extends AppEntity
{
    public string $strEntityName       = "Mobiniti";
    public $strDatabaseTable    = "mobiniti_campaign";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = MobinitiCampaignModel::class;
    public $strMainModelPrimary = "id";
}