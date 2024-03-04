<?php

namespace Entities\Users\Models;

use App\Core\AppModel;
use App\Utilities\Database;
use App\Utilities\Excell\ExcellCollection;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Companies\Classes\Departments\Departments;
use Entities\Companies\Classes\Departments\DepartmentTicketQueues;
use Entities\Users\Classes\Connections;
use Entities\Users\Classes\UserClass;

/**
 * @property int $user_id
 * @property int $division_id
 * @property int $company_id
 * @property int $sponsor_id
 * @property string $username
 * @property string $password
 * @property string $password_reset_token
 * @property int $pin
 * @property int $user_email
 * @property int $user_phone
 * @property string $created_on
 * @property int $created_by
 * @property string $last_updated
 * @property int $updated_by
 * @property string $status
 * @property string $name_prefx
 * @property string $first_name
 * @property string $middle_name
 * @property string $last_name
 * @property string $name_sufx
 * @property string $preferred_name
 * @property string $last_login
 * @property string $sys_row_id
 */

class UserModel extends AppModel
{
    protected string $EntityName = "Users";
    protected string $ModelName = "User";
    protected $originatingUser;

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    public function isAdmin()
    {
        $userRoleClass = $this->Roles !== null ? ($this->Roles->FindEntityByKey("user_class_type_id")->user_class_type_id ?? null) : null;
        if (!userIsCustomPlatform($userRoleClass))
        {
            return false;
        }

        return true;
    }

    private function loadDefinitions(): array
    {
        return [
            "user_id" => ["type" => "int", "length" => 15],
            "division_id" => ["type" => "int", "length" => 15, "fk" => ["table" => "division", "key" => "division_id", "value" => "division_name"]],
            "company_id" => ["type" => "int", "length" => 15, "fk" => ["table" => "company", "key" => "company_id", "value" => "company_name"]],
            "sponsor_id" => ["type" => "int", "length" => 15, "nullable" => true, "fk" => ["table" => "user", "key" => "user_id", "value" => "username"]],
            "username" => ["type" => "varchar", "length" => 35],
            "password" => ["type" => "varchar", "length" => 250],
            "password_reset_token" => ["type" => "char", "length" => 36, "nullable" => true],
            "pin" => ["type" => "int", "length" => 6, "nullable" => true],
            "user_email" => ["type" => "int", "length" => 15, "nullable" => true, "fk" => ["table" => "connection", "key" => "connection_id", "value" => "connection_value"]],
            "user_phone" => ["type" => "int", "length" => 15, "nullable" => true, "fk" => ["table" => "connection", "key" => "connection_id", "value" => "connection_value"]],
            "created_on" => ["type" => "datetime", "length" => 0],
            "created_by" => ["type" => "int", "length" => 15, "nullable" => true, "fk" => ["table" => "user", "key" => "user_id", "value" => "username"]],
            "last_updated" => ["type" => "datetime", "length" => 0],
            "updated_by" => ["type" => "int", "length" => 15, "nullable" => true, "fk" => ["table" => "user", "key" => "user_id", "value" => "username"]],
            "status" => ["type" => "varchar", "length" => 15],
            "name_prefx" => ["type" => "varchar", "length" => 20],
            "first_name" => ["type" => "varchar", "length" => 50],
            "middle_name" => ["type" => "varchar", "length" => 45],
            "last_name" => ["type" => "varchar", "length" => 50],
            "name_sufx" => ["type" => "varchar", "length" => 20],
            "preferred_name" => ["type" => "varchar", "length" => 50],
            "last_login" => ["type" => "datetime", "length" => 0, "nullable" => true],
            "sys_row_id" => ["type" => "char", "length" => 36, "nullable" => true]
        ];
    }

    public function LoadUserConnections() : self
    {
        if (!empty($this->Connections) && is_a($this->Connections, \App\Utilities\Excell\ExcellCollection::class))
        {
            return $this;
        }

        global $app;
        $colUserConnectionsResult = $this->getConnectionsByUserId($this->user_id, $app->objCustomPlatform->getCompanyId());

        $this->AddUnvalidatedValue("Connections", $colUserConnectionsResult->data);

        return $this;
    }

    public function getConnectionsByUserId(int $userId, int $companyId) : ExcellTransaction
    {
        $strCardConnectionsQuery = "
            SELECT 
                cn.connection_id,
                cn.user_id, 
                cn.company_id, 
                cnt.name AS connection_type_name,
                cn.connection_type_id, 
                cn.connection_value, 
                cn.is_primary, 
                cn.connection_class, 
                cnt.action AS default_action,
                cnt.font_awesome,
                (SELECT COUNT(*) FROM connection_rel cr WHERE cr.connection_id = cn.connection_id) as cards
            FROM excell_main.connection cn 
            LEFT JOIN  excell_main.connection_type cnt ON cnt.connection_type_id = cn.connection_type_id 
            WHERE cn.user_id = {$userId} AND cn.company_id = {$companyId} ORDER BY cn.connection_id ASC;";

        $colCardConnectionsResult = Database::getSimple($strCardConnectionsQuery);
        $colCardConnectionsResult->getData()->HydrateModelData(ConnectionModel::class, true);

        return $colCardConnectionsResult;
    }

    public function loadRoles() : self
    {
        if (!is_numeric($this->user_id)) {
            return $this;
        }

        $objUserClassResult = (new UserClass())->getFks()->getWhere(["user_id" => $this->user_id]);
        if ($objUserClassResult->result->Success === true && $objUserClassResult->result->Count > 0)
        {
            $this->AddUnvalidatedValue("Roles", $objUserClassResult->data);
        }

        return $this;
    }

    public function loadDepartments() : self
    {
        $companyDepartmentResult = (new Departments())->getByUserId($this->user_id);
        if ($companyDepartmentResult->result->Success === true)
        {
            $this->AddUnvalidatedValue("Departments", $companyDepartmentResult->data);

            $ticketQueues = new DepartmentTicketQueues();
            $ticketQueueResult = $ticketQueues->getByUserAndDepartmentIds($this->user_id, $this->Departments->FieldsToArray(["company_department_id"]));

            if ($ticketQueueResult->result->Count > 0)
            {
                $this->Departments->Foreach(function($currDepartment) use ($ticketQueueResult)
                {
                    foreach($ticketQueueResult->data as $currTicketQueue)
                    {
                        if ($currTicketQueue->company_department_id === $currDepartment->company_department_id)
                        {

                            if (!is_a($currDepartment->ticketQueue, ExcellCollection::class))
                            {
                                $currDepartment->AddUnvalidatedValue("ticketQueue", new ExcellCollection());
                            }

                            $currDepartment->ticketQueue->Add($currTicketQueue);
                        }
                    }

                    return $currDepartment;
                });

                $this->AddUnvalidatedValue("departmentTicketQueuesCount", $ticketQueueResult->result->Count);
            }
        }
        return $this;
    }
}