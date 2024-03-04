<?php

namespace Entities\Modules\Classes;

use App\Core\AppEntity;
use App\Utilities\Database;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Cards\Models\AppInstancesModel;
use Entities\Modules\Models\AppInstanceRelModel;

class AppInstanceRels extends AppEntity
{
    public string $strEntityName       = "Modules";
    public $strDatabaseTable    = "app_instance_rel";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = AppInstanceRelModel::class;
    public $strMainModelPrimary = "app_instance_rel_id";

    public function getByPageRelId($cardPageRelId) : ExcellTransaction
    {
        $strCardWidgetQuery = "
            SELECT 
                air.app_instance_rel_id, 
                air.app_instance_id, 
                air.card_page_id, 
                air.card_page_rel_id, 
                ai.module_app_id,
                ma.module_id, 
                ma.app_uuid, 
                ma.name as app_name, 
                ma.author as app_author, 
                ma.domain as app_domain, 
                ma.version,
                ma.logo,
                ai.instance_uuid as instance_uuid, 
                maw1.module_app_widget_id AS widget_portal_id,
                maw1.name AS widget_portal_name,
                maw1.endpoint AS widget_portal_endpoint,
                maw2.name AS widget_page_name,
                maw2.module_app_widget_id AS widget_page_id,
                maw2.endpoint AS widget_page_endpoint
            FROM 
                excell_main.app_instance_rel air 
            LEFT JOIN 
                excell_main.app_instance ai ON ai.app_instance_id = air.app_instance_id 
            LEFT JOIN 
                excell_modules.module_apps ma ON ai.module_app_id = ma.module_app_id 
            LEFT JOIN 
                excell_modules.module_app_widgets maw1 ON maw1.module_app_id = ma.module_app_id AND maw1.widget_class = 1002
            LEFT JOIN 
                excell_modules.module_app_widgets maw2 ON maw2.module_app_id = ma.module_app_id AND maw2.module_app_widget_id = air.module_app_widget_id
            WHERE 
                air.card_page_rel_id = " . $cardPageRelId . ";";

        $appInstanceRelResult = Database::getSimple($strCardWidgetQuery,"card_page_rel_id");
        $appInstanceRelResult->getData()->HydrateModelData(AppInstanceRelModel::class, true);

        return $appInstanceRelResult;
    }

    public function getByPageIds(array $cardPageIds) : ExcellTransaction
    {
        $strCardWidgetQuery = "
            SELECT 
                air.app_instance_rel_id, 
                air.app_instance_id, 
                air.card_page_id, 
                air.card_page_rel_id, 
                ai.module_app_id,
                ma.module_id, 
                ma.app_uuid, 
                ma.name as app_name, 
                ma.author as app_author, 
                ma.domain as app_domain, 
                ma.version,
                ma.logo,
                ai.instance_uuid as instance_uuid, 
                maw1.module_app_widget_id AS widget_portal_id,
                maw1.name AS widget_portal_name,
                maw1.endpoint AS widget_portal_endpoint,
                maw2.name AS widget_page_name,
                maw2.module_app_widget_id AS widget_page_id,
                maw2.endpoint AS widget_page_endpoint
            FROM 
                excell_main.app_instance_rel air 
            LEFT JOIN 
                excell_main.app_instance ai ON ai.app_instance_id = air.app_instance_id 
            LEFT JOIN 
                excell_modules.module_apps ma ON ai.module_app_id = ma.module_app_id 
            LEFT JOIN 
                excell_modules.module_app_widgets maw1 ON maw1.module_app_id = ma.module_app_id AND maw1.widget_class = 1002
            LEFT JOIN 
                excell_modules.module_app_widgets maw2 ON maw2.module_app_id = ma.module_app_id AND maw2.module_app_widget_id = air.module_app_widget_id
            WHERE 
                air.card_page_id IN (" . implode(",", $cardPageIds) . ");";

        $appInstanceRelResult = Database::getSimple($strCardWidgetQuery,"card_page_rel_id");
        $appInstanceRelResult->getData()->HydrateModelData(AppInstanceRelModel::class, true);

        return $appInstanceRelResult;
    }

    public function getByCardId($cardId, array $classIds) : ExcellTransaction
    {
        $strCardWidgetQuery = "
            SELECT 
                air.app_instance_rel_id, 
                air.app_instance_id, 
                air.card_page_id, 
                air.card_page_rel_id, 
                ai.module_app_id,
                ma.module_id, 
                ma.app_uuid, 
                ma.name as app_name, 
                ma.author as app_author, 
                ma.domain as app_domain, 
                ma.version,
                ma.logo,
                ai.instance_uuid as instance_uuid, 
                maw1.module_app_widget_id AS widget_portal_id,
                maw1.name AS widget_portal_name,
                maw1.endpoint AS widget_portal_endpoint,
                maw2.name AS widget_page_name,
                maw2.module_app_widget_id AS widget_page_id,
                maw2.endpoint AS widget_page_endpoint
            FROM 
                excell_main.app_instance_rel air 
            LEFT JOIN 
                excell_main.app_instance ai ON ai.app_instance_id = air.app_instance_id 
            LEFT JOIN 
                excell_modules.module_apps ma ON ai.module_app_id = ma.module_app_id 
            LEFT JOIN 
                excell_modules.module_app_widgets maw1 ON maw1.module_app_id = ma.module_app_id AND maw1.widget_class IN (" . implode(",", $classIds) . ")
            LEFT JOIN 
                excell_modules.module_app_widgets maw2 ON maw2.module_app_id = ma.module_app_id AND maw2.module_app_widget_id = air.module_app_widget_id
            WHERE 
                air.card_id = " . $cardId . ";";

        $appInstanceRelResult = Database::getSimple($strCardWidgetQuery,"card_page_rel_id");
        $appInstanceRelResult->getData()->HydrateModelData(AppInstanceRelModel::class, true);

        return $appInstanceRelResult;
    }
}
