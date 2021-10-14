<?php

namespace Entities\Companies\Classes;

use App\Core\AppEntity;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Companies\Models\CompanySettingModel;

class CompanySettings extends AppEntity
{
    public $strEntityName       = "Companies";
    public $strDatabaseTable    = "company_setting";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = CompanySettingModel::class;
    public $strMainModelPrimary = "company_setting_id";

    public function getByCompanyId($companyId) : ExcellTransaction
    {
        return $this->getWhere(["company_id" => $companyId]);
    }
}
