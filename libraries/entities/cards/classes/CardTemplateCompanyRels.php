<?php

namespace Entities\Cards\Classes;

use App\Core\AppEntity;
use Entities\Cards\Models\CardTemplateCompanyRelModel;

class CardTemplateCompanyRels extends AppEntity
{
    public string $strEntityName = "Cards";
    public $strDatabaseTable    = "card_template_company_rel";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = CardTemplateCompanyRelModel::class;
    public $strMainModelPrimary = "card_template_company_rel_id";
}

