<?php

namespace Entities\Cards\Classes;

use App\Core\AppEntity;
use Entities\Cards\Models\CardAddonModel;

class CardAddon extends AppEntity
{
    public $strEntityName       = "Cards";
    public $strDatabaseTable    = "card_addon";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = CardAddonModel::class;
    public $strMainModelPrimary = "card_addon_id";
}