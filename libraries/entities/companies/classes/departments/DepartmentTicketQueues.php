<?php

namespace Entities\Companies\Classes\Departments;

use App\Core\AppEntity;
use App\Utilities\Database;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Companies\Models\Departments\DepartmentModel;
use Entities\Companies\Models\Departments\DepartmentTicketQueueModel;

class DepartmentTicketQueues extends AppEntity
{
    public $strEntityName       = "Companies";
    public $strDatabaseTable    = "ticket_queue";
    public $strDatabaseName     = "Crm";
    public $strMainModelName    = DepartmentTicketQueueModel::class;
    public $strMainModelPrimary = "ticket_queue_id";

    public function getByUserAndDepartmentIds($userId, array $departmentIds) : ExcellTransaction
    {
        $objWhereClause = "
            SELECT tq.*,
                (SELECT dur.department_user_role_id FROM excell_main.company_department_user_role dur WHERE dur.department_user_role_id = utr.department_user_role) AS role_id,
                (SELECT dur.label FROM excell_main.company_department_user_role dur WHERE dur.department_user_role_id = utr.department_user_role) AS user_role_label,
                (SELECT dur.abbreviation FROM excell_main.company_department_user_role dur WHERE dur.department_user_role_id = utr.department_user_role) AS user_role_abbr
            FROM excell_crm.ticket_queue tq
            LEFT JOIN excell_main.company_department_user_ticketqueue_role utr ON utr.ticket_queue_id = tq.ticket_queue_id ";

        $objWhereClause .= "WHERE utr.user_id = '" . $userId . "' AND tq.company_department_id IN (".implode(",", $departmentIds).")";

        $departmentResult = Database::getSimple($objWhereClause, "ticket_queue_id");
        $departmentResult->Data->HydrateModelData(DepartmentTicketQueueModel::class, true);

        if ($departmentResult->Result->Count !== 1)
        {
            return new ExcellTransaction(false, $departmentResult->Result->Message, ["errors" => [$departmentResult->Result->Message]]);
        }

        return $departmentResult;
    }
}