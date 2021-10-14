<?php

namespace Entities\Cards\Classes;

use App\Core\AppEntity;
use Entities\Cards\Models\CardPageAppPropertyModel;

class CardPageAppProperty extends AppEntity
{
    public $strEntityName       = "Cards";
    public $strDatabaseTable    = "card_tab_app_property";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = CardPageAppPropertyModel::class;
    public $strMainModelPrimary = "card_tab_app_property_id";
}