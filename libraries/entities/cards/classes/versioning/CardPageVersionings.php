<?php

namespace Entities\Cards\Classes\Versioning;

use App\Core\AppEntity;
use Entities\Cards\Models\CardPageVersioningModel;

class CardPageVersionings extends AppEntity
{
    public function __construct()
    {
        parent::__construct();
    }

    public string $strEntityName       = "Cards";
    public $strDatabaseTable    = "card_page_versioning";
    public $strDatabaseName     = "Versioning";
    public $strMainModelName    = CardPageVersioningModel::class;
    public $strMainModelPrimary = "card_page_version_id";
}