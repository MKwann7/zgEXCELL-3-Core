<?php

namespace Entities\Cards\Classes;

use App\Core\AppController;
use App\Core\AppEntity;
use Entities\Cards\Models\CardTemplateModel;

class CardTemplates extends AppEntity
{
    public $strEntityName       = "Cards";
    public $strDatabaseTable    = "card_template";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = CardTemplateModel::class;
    public $strMainModelPrimary = "card_template_id";
}

