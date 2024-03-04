<?php

namespace Entities\Cards\Classes;

use App\Core\AppEntity;
use App\Utilities\Database;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Cards\Models\CardTemplateModel;

class CardTemplates extends AppEntity
{
    public string $strEntityName       = "Cards";
    public $strDatabaseTable    = "card_template";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = CardTemplateModel::class;
    public $strMainModelPrimary = "card_template_id";

    public function getByCompanyId(int $companyId, string $type) : ExcellTransaction
    {
        $objWhereClause = "
            SELECT ct.*
            FROM card_template_company_rel ctcr
            LEFT JOIN card_template ct ON ct.card_template_id = ctcr.card_template_id
            WHERE ctcr.company_id = ".$companyId." AND ct.template_type = '".$type."'";

        $cardTemplateResult = Database::getSimple($objWhereClause, "card_template_id");
        $cardTemplateResult->getData()->HydrateModelData(CardTemplateModel::class, true);

        return $cardTemplateResult;
    }
}

