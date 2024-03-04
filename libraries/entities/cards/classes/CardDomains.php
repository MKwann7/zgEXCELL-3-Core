<?php

namespace Entities\Cards\Classes;

use App\Core\AppEntity;
use Entities\Cards\Models\CardDomainModel;

class CardDomains extends AppEntity
{
    public string $strEntityName       = "Cards";
    public $strDatabaseTable    = "card_domain";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = CardDomainModel::class;
    public $strMainModelPrimary = "card_domain_id";
}