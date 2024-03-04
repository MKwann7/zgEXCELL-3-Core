<?php

namespace Entities\Cards\Classes;

use App\Core\AppEntity;
use Entities\Cards\Models\CardVersioningModel;

class CardVersionings extends AppEntity
{
    public function __construct()
    {
        parent::__construct();
    }

    public string $strEntityName       = "Cards";
    public $strDatabaseTable    = "card_versioning";
    public $strDatabaseName     = "Versioning";
    public $strMainModelName    = CardVersioningModel::class;
    public $strMainModelPrimary = "card_version_id";
    public $isPrimaryModule     = true;
}