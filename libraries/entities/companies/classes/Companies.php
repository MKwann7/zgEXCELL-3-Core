<?php

namespace Entities\Companies\Classes;

use App\Core\AppEntity;
use App\Utilities\Database;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Companies\Models\CompanyModel;

class Companies extends AppEntity
{
    public string $strEntityName       = "Companies";
    public $strDatabaseTable    = "company";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = CompanyModel::class;
    public $strMainModelPrimary = "company_id";
    public $isPrimaryModule     = true;

    const APP_TYPE_DEFAULT = "default";
    const APP_TYPE_MAXTECH = "maxtech";

    public $intDefaultSponsor = 726;

    public function getByUuid($uuid) : ExcellTransaction
    {
        $objWhereClause = "
            SELECT cp.*,
            cp.platform_name AS platform,
            cp.domain_portal AS portal_domain,
            cp.domain_public AS public_domain,
            (SELECT CONCAT(user.first_name, ' ', user.last_name) FROM `excell_main`.`user` WHERE user.user_id = cp.owner_id LIMIT 1) AS owner,
            (SELECT COUNT(*) FROM `excell_main`.`card` cd WHERE cd.company_id = cp.company_id) AS cards,
            (SELECT value FROM `excell_financial`.`user_payment_property` upp WHERE cp.company_id = upp.company_id AND cp.owner_id = upp.user_id AND upp.type_id = 2 AND upp.state = 'all' LIMIT 1) AS platform_stripe_id
            FROM `excell_main`.`company` cp ";

        $objWhereClause .= "WHERE cp.sys_row_id = '".$uuid."'";

        $customPlatformResult = Database::getSimple($objWhereClause, "company_id");
        $customPlatformResult->getData()->HydrateModelData(CompanyModel::class, true);

        if ($customPlatformResult->result->Count !== 1)
        {
            return new ExcellTransaction(false, $customPlatformResult->result->Message, ["errors" => [$customPlatformResult->result->Message, $objWhereClause]]);
        }
        return $customPlatformResult;
    }
}