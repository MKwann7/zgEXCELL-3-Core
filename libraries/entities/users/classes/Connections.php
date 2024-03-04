<?php

namespace Entities\Users\Classes;

use App\Core\AppController;
use App\Core\AppEntity;
use App\Utilities\Database;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Users\Models\ConnectionModel;

class Connections extends AppEntity
{
    public string $strEntityName       = "Users";
    public $strDatabaseTable    = "connection";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = ConnectionModel::class;
    public $strMainModelPrimary = "connection_id";

    public function getById($connectionId) : ExcellTransaction
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
                cnt.font_awesome 
            FROM excell_main.connection cn 
            LEFT JOIN  excell_main.connection_type cnt ON cnt.connection_type_id = cn.connection_type_id 
            WHERE cn.connection_id = {$connectionId} ORDER BY cn.connection_id ASC;";

        $colCardConnectionsResult = Database::getSimple($strCardConnectionsQuery);
        $colCardConnectionsResult->getData()->HydrateModelData(ConnectionModel::class, true);

        return $colCardConnectionsResult;
    }

    public function getByCardId($cardId) : ExcellTransaction
    {
        $strCardConnectionsQuery = "
            SELECT 
                cnr1.*,
                cn.user_id, 
                cn.company_id, 
                cnt.name AS connection_type_name,
                cn.connection_type_id, 
                cn.connection_value, 
                cn.is_primary, 
                cn.connection_class, 
                cnt.action AS default_action,
                cnt.font_awesome 
            FROM excell_main.connection_rel cnr1 
            JOIN (SELECT MAX(cnrx.connection_rel_id) AS most_recent_rel, cnrx.connection_rel_id FROM excell_main.connection_rel cnrx GROUP BY cnrx.connection_rel_id) cnr3
            JOIN excell_main.connection_rel cnr2 ON (cnr1.connection_rel_id = cnr3.most_recent_rel && cnr2.connection_rel_id = cnr3.most_recent_rel)
            LEFT JOIN excell_main.connection cn ON cn.connection_id = cnr1.connection_id 
            LEFT JOIN  excell_main.connection_type cnt ON cnt.connection_type_id = cn.connection_type_id 
            WHERE cnr1.card_id = {$cardId} ORDER BY cnr1.display_order ASC;";

        $colCardConnectionsResult = Database::getSimple($strCardConnectionsQuery);
        $colCardConnectionsResult->getData()->HydrateModelData(ConnectionModel::class, true);

        return $colCardConnectionsResult;
    }

    public function getSharesByCardId($cardId) : ExcellTransaction
    {
        $strCardConnectionsQuery = "
            SELECT 
                cnr1.*,
                cn.user_id, 
                cn.company_id, 
                cnt.name AS connection_type_name,
                cn.connection_type_id, 
                cn.connection_value, 
                cn.is_primary, 
                cn.connection_class, 
                cnt.action AS default_action,
                cnt.font_awesome 
            FROM excell_main.connection_rel cnr1 
            JOIN (SELECT MAX(cnrx.connection_rel_id) AS most_recent_rel, cnrx.connection_rel_id FROM excell_main.connection_rel cnrx GROUP BY cnrx.connection_rel_id) cnr3
            JOIN excell_main.connection_rel cnr2 ON (cnr1.connection_rel_id = cnr3.most_recent_rel && cnr2.connection_rel_id = cnr3.most_recent_rel)
            LEFT JOIN excell_main.connection cn ON cn.connection_id = cnr1.connection_id 
            LEFT JOIN  excell_main.connection_type cnt ON cnt.connection_type_id = cn.connection_type_id 
            WHERE 
                cnr1.card_id = {$cardId} AND
                (cnt.action IN ('phone', 'fax', 'email', 'sms') OR cnt.connection_type_id IN (2,7))
            ORDER BY cnr1.display_order ASC;";

        $colCardConnectionsResult = Database::getSimple($strCardConnectionsQuery);
        $colCardConnectionsResult->getData()->HydrateModelData(ConnectionModel::class, true);

        return $colCardConnectionsResult;
    }

    public function getSocialMediaByCardId($cardId) : ExcellTransaction
    {
        $strCardConnectionsQuery = "
            SELECT 
                cnr1.*,
                cn.user_id, 
                cn.company_id, 
                cnt.name AS connection_type_name,
                cn.connection_type_id, 
                cn.connection_value, 
                cn.is_primary, 
                cn.connection_class, 
                cnt.action AS default_action,
                cnt.font_awesome 
            FROM excell_main.connection_rel cnr1 
            JOIN (SELECT MAX(cnrx.connection_rel_id) AS most_recent_rel, cnrx.connection_rel_id FROM excell_main.connection_rel cnrx GROUP BY cnrx.connection_rel_id) cnr3
            JOIN excell_main.connection_rel cnr2 ON (cnr1.connection_rel_id = cnr3.most_recent_rel && cnr2.connection_rel_id = cnr3.most_recent_rel)
            LEFT JOIN excell_main.connection cn ON cn.connection_id = cnr1.connection_id 
            LEFT JOIN  excell_main.connection_type cnt ON cnt.connection_type_id = cn.connection_type_id 
            WHERE 
                cnr1.card_id = {$cardId} AND
                (cnt.action IN ('link') AND cnt.connection_type_id NOT IN (2,7))
            ORDER BY cnr1.display_order ASC;";

        $colCardConnectionsResult = Database::getSimple($strCardConnectionsQuery);
        $colCardConnectionsResult->getData()->HydrateModelData(ConnectionModel::class, true);

        return $colCardConnectionsResult;
    }

    public function getByUserIds(array $userIds, $loggedInUser = null, $companyId = 0) : ExcellTransaction
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
                usr.first_name, 
                usr.last_name 
            FROM excell_main.connection cn 
            LEFT JOIN  excell_main.connection_type cnt ON cnt.connection_type_id = cn.connection_type_id 
            LEFT JOIN  excell_main.user usr ON cn.user_id = usr.user_id 
            WHERE (cn.user_id IN (" . implode(",", $userIds) . ") AND cn.company_id = {$companyId})";

        if ($loggedInUser !== null)
        {
            $strCardConnectionsQuery .= " OR (cn.user_id IN (" . $loggedInUser . "))";
        }

        $strCardConnectionsQuery .= " ORDER BY cn.connection_id DESC;";

        $colCardConnectionsResult = Database::getSimple($strCardConnectionsQuery);
        $colCardConnectionsResult->getData()->HydrateModelData(ConnectionModel::class, true);

        return $colCardConnectionsResult;
    }

    public function getSharesByUserIds(array $userIds, $loggedInUser = null, $companyId = 0) : ExcellTransaction
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
                usr.first_name, 
                usr.last_name 
            FROM excell_main.connection cn 
            LEFT JOIN  excell_main.connection_type cnt ON cnt.connection_type_id = cn.connection_type_id 
            LEFT JOIN  excell_main.user usr ON cn.user_id = usr.user_id 
            WHERE 
                (cn.user_id IN (" . implode(",", $userIds) . ") AND  cn.company_id = {$companyId} AND (cnt.action IN ('phone', 'fax', 'email', 'sms') OR cnt.connection_type_id IN (2,7)))";

            if ($loggedInUser !== null)
            {
                $strCardConnectionsQuery .= " OR (cn.user_id IN (" . $loggedInUser . ") AND (cnt.action IN ('phone', 'fax', 'email', 'sms') OR cnt.connection_type_id IN (2,7)))";
            }

            $strCardConnectionsQuery .= "ORDER BY cn.connection_id DESC;";

        $colCardConnectionsResult = Database::getSimple($strCardConnectionsQuery);
        $colCardConnectionsResult->getData()->HydrateModelData(ConnectionModel::class, true);

        return $colCardConnectionsResult;
    }

    public function getSocialMediaByUserIds(array $userIds, $loggedInUser = null, $companyId = null) : ExcellTransaction
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
                usr.first_name, 
                usr.last_name 
            FROM excell_main.connection cn 
            LEFT JOIN  excell_main.connection_type cnt ON cnt.connection_type_id = cn.connection_type_id 
            LEFT JOIN  excell_main.user usr ON cn.user_id = usr.user_id 
            WHERE 
                cn.user_id IN (" . implode(",", $userIds) . ") AND
                (cnt.action IN ('link') AND cnt.connection_type_id NOT IN (2,7))
            ORDER BY cn.connection_id DESC;";

        $colCardConnectionsResult = Database::getSimple($strCardConnectionsQuery);
        $colCardConnectionsResult->getData()->HydrateModelData(ConnectionModel::class, true);

        return $colCardConnectionsResult;
    }
}