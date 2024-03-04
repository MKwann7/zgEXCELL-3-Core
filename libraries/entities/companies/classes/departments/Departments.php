<?php

namespace Entities\Companies\Classes\Departments;

use App\Core\AppEntity;
use App\Utilities\Database;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Cards\Models\CardModel;
use Entities\Companies\Models\Departments\DepartmentModel;

class Departments extends AppEntity
{
    public string $strEntityName       = "Companies";
    public $strDatabaseTable    = "company_department";
    public $strMainModelName    = DepartmentModel::class;
    public $strMainModelPrimary = "company_department_id";

    public function getByUserId($userId) : ExcellTransaction
    {
        $objWhereClause = "
            SELECT dp.*
            FROM excell_main.company_department dp
            LEFT JOIN excell_main.company_department_user_rel dpur ON dpur.department_id = dp.company_department_id ";

        $objWhereClause .= "WHERE dpur.user_id = '".$userId."'";

        $departmentResult = Database::getSimple($objWhereClause, "`company_department_id`");
        $departmentResult->getData()->HydrateModelData(DepartmentModel::class, true);

        if ($departmentResult->result->Count === 0)
        {
            return new ExcellTransaction(false, $departmentResult->result->Message, ["errors" => [$departmentResult->result->Message]]);
        }

        return $departmentResult;
    }
}

