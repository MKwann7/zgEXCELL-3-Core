<?php

namespace Entities\Modules\Classes;

use App\Core\AppEntity;
use App\Utilities\Database;
use App\Utilities\Http\Http;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Modules\Models\ModuleMainModel;
use Entities\Modules\Models\ModuleAppModel;
use Modules\Ezcard\Widgets\MemberDirectory\Classes\EzcardMemberDirectories;
use Modules\Ezcard\Widgets\MemberDirectory\Models\EzcardMemberDirectoryModel;

class ModuleApps extends AppEntity
{
    public $strEntityName       = "Modules";
    public $strDatabaseTable    = "module_apps";
    public $strDatabaseName     = "Modules";
    public $strMainModelName    = ModuleAppModel::class;
    public $strMainModelPrimary = "module_app_id";

    public const createPageInstanceEndpoint = "create-instance-for-page";

    public function getLatestModuleWidgetsById($intEntityId) : ExcellTransaction
    {
        $whereClause = "mw1.module_app_id = {$intEntityId}";

        return $this->getLatestModuleWidgets($whereClause);
    }

    public function getLatestModuleWidgetsByUuid($uuid) : ExcellTransaction
    {
        $whereClause = "mw1.app_uuid = '{$uuid}'";

        return $this->getLatestModuleWidgets($whereClause);
    }

    public function getLatestModuleWidgetsByUuidAndVersion($uuid, $version) : ExcellTransaction
    {
        $whereClause = "mw1.app_uuid = '{$uuid}' && mw1.version = '{$version}'";

        return $this->getLatestModuleWidgets($whereClause);
    }

    protected function getLatestModuleWidgets($whereClause) : ExcellTransaction
    {
        $strModuleQuery = "SELECT mw1.*, m1.name AS module_name ".
            "FROM excell_modules.module_apps mw1 " .
            "JOIN (SELECT COUNT(mw3x.version) AS versionCount, mw3x.app_uuid FROM excell_modules.module_apps mw3x GROUP BY mw3x.app_uuid) mw3 " .
            "JOIN excell_modules.module_apps mw2 ON ((mw1.app_uuid = mw2.app_uuid AND mw1.version > mw2.version) OR (mw1.app_uuid = mw2.app_uuid AND mw3.app_uuid = mw2.app_uuid AND mw3.versionCount = 1)) " .
            "JOIN (SELECT m1.* FROM excell_modules.modules m1 JOIN excell_modules.modules m2 ON m1.module_uuid = m2.module_uuid AND m1.version > m2.version ORDER BY m1.name ASC) m1 " .
            "WHERE " . $whereClause;

        $objModuleResult = Database::getSimple($strModuleQuery,"module_app_id");

        if ($objModuleResult->Result->Success === false)
        {
            return $objModuleResult;
        }

        $objModuleResult->Data->HydrateModelData(ModuleAppModel::class, true);

        $objModuleWidgets = new ModuleAppWidgets();
        $colModuleComponents = $objModuleWidgets->getWhereIn("module_app_id", $objModuleResult->Data->FieldsToArray(["module_app_id"]))->Data;

        $objModuleResult->Data->HydrateChildModelData("widgets", ["module_app_id" => "module_app_id"], $colModuleComponents);

        return $objModuleResult;
    }

    public function getLatestModuleWidgetsByNameAsc() : ExcellTransaction
    {
        $strModuleQuery = "SELECT mw1.*, m1.name AS module_name ".
            "FROM excell_modules.module_apps mw1 " .
            "JOIN (SELECT COUNT(mw3x.version) AS versionCount, mw3x.app_uuid FROM excell_modules.module_apps mw3x GROUP BY mw3x.app_uuid) mw3 " .
            "JOIN excell_modules.module_apps mw2 ON ((mw1.app_uuid = mw2.app_uuid AND mw1.version > mw2.version) OR (mw1.app_uuid = mw2.app_uuid AND mw3.app_uuid = mw2.app_uuid AND mw3.versionCount = 1)) " .
            "JOIN (SELECT m1.* FROM excell_modules.modules m1 JOIN excell_modules.modules m2 ON m1.module_uuid = m2.module_uuid AND m1.version > m2.version ORDER BY m1.name ASC) m1 " .
            "ON mw1.module_id = m1.module_id ORDER BY mw1.name ASC";

        $objModuleResult = Database::getSimple($strModuleQuery,"module_app_id");

        if ($objModuleResult->Result->Success === false)
        {
            return $objModuleResult;
        }

        $objModuleResult->Data->HydrateModelData(ModuleAppModel::class, true);

        $objModuleComponents = new ModuleAppWidgets();
        $colModuleComponents = $objModuleComponents->getWhereIn("module_app_id", $objModuleResult->Data->FieldsToArray(["module_app_id"]))->Data;

        $objModuleEndpoints = new ModuleAppEndpoints();
        $colModuleEndpoints = $objModuleEndpoints->getWhereIn("module_app_id", $objModuleResult->Data->FieldsToArray(["module_app_id"]))->Data;

        $objModuleResult->Data->HydrateChildModelData("components", ["module_app_id" => "module_app_id"], $colModuleComponents);
        $objModuleResult->Data->HydrateChildModelData("endpoints", ["module_app_id" => "module_app_id"], $colModuleEndpoints);

        return $objModuleResult;
    }

    public function getLatestConfiguration($objModuleWidget, $configId) : ExcellTransaction
    {
        $configurationEndpoint = $objModuleWidget->widgets->FindEntityByValue("widget_class", $configId);

        if ($configurationEndpoint === null)
        {
            return new ExcellTransaction(false,"Configuration endpoint not found for " . $objModuleWidget->name . "." );
        }

        return $this->makeWidgetEndpointRequest("get", $objModuleWidget->domain, $configurationEndpoint->endpoint, [], ["widget_id" => $objModuleWidget->app_uuid, "instance_id" => "new", "user_id" => ($this->app->getActiveLoggedInUser()->sys_row_id ?? getGuid()), "platform_id" => $this->app->objCustomPlatform->getCompany()->sys_row_id, "platform_url" => $this->app->objCustomPlatform->getFullPublicDomain(), "platform_name" => $this->app->objCustomPlatform->getCompany()->platform_name]);
    }

    public function getLatestWidgetContentForPage($cardId, $cardPageRel) : ExcellTransaction
    {
        return $this->makeWidgetEndpointRequest("get", $cardPageRel->__app->app_domain, $cardPageRel->__app->widget_page_endpoint, [], ["widget_id" => $cardPageRel->__app->app_uuid, "instance_id" => $cardPageRel->__app->widget_instance_uuid, "card_id" => $cardId, "user_id" => ($this->app->getActiveLoggedInUser()->sys_row_id ?? getGuid()), "platform_id" => $this->app->objCustomPlatform->getCompany()->sys_row_id, "platform_url" => $this->app->objCustomPlatform->getFullPublicDomain(), "platform_name" => $this->app->objCustomPlatform->getCompany()->platform_name]);
    }

    public function getLatestWidgetContentForCard($cardId, $cardApp) : ExcellTransaction
    {
        return $this->makeWidgetEndpointRequest("get", $cardApp->app_domain, $cardApp->widget_page_endpoint, [], ["widget_id" => $cardApp->app_uuid, "instance_id" => $cardApp->widget_instance_uuid, "card_id" => $cardId, "user_id" => ($this->app->getActiveLoggedInUser()->sys_row_id ?? getGuid()), "platform_id" => $this->app->objCustomPlatform->getCompany()->sys_row_id, "platform_url" => $this->app->objCustomPlatform->getFullPublicDomain(), "platform_name" => $this->app->objCustomPlatform->getCompany()->platform_name]);
    }

    protected function makeWidgetEndpointRequest($verb, $domain, $endpoint, $postData = [], $parameters = "") : ExcellTransaction
    {
        $objHttp = new Http();
        $configurationEndpoint = $domain . "/" . $endpoint . "?" . http_build_query($parameters);

        try
        {
            $objHttpRequest = $objHttp->newRequest(
                $verb,
                $configurationEndpoint,
                $postData
            )
                ->setOption(CURLOPT_USERPWD, env("MODULE_USERNAME") . ':' . env("MODULE_PASSWORD"))
                ->setOption(CURLOPT_SSL_VERIFYPEER, false);

            $objHttpResponse = $objHttpRequest->send();

            if ($objHttpResponse->statusCode !== 200)
            {
                return new ExcellTransaction(false,"Received [{$objHttpResponse->statusCode}] status code from module configuration endpoint " . $configurationEndpoint . "." );
            }

            if (empty($objHttpResponse->body))
            {
                return new ExcellTransaction(false,"No body returned from module configuration endpoint " . $configurationEndpoint . "." );
            }

            $objModuleConfigurationResult = json_decode($objHttpResponse->body);

            return new ExcellTransaction(true, "Success", $objModuleConfigurationResult, 1, [], $configurationEndpoint);

        } catch(\Exception $ex)
        {
            return new ExcellTransaction(false,"Exception Throw: " . $ex);
        }
    }

    public function syncModule($moduleSyncData, ModuleMainModel $module) : ExcellTransaction
    {

    }

    public function createNewModuleAppInstance($uuid, $moduleUuid) : ExcellTransaction
    {
        return $this->createNewInstanceWithModuleApp($uuid, $moduleUuid, self::createPageInstanceEndpoint);

    }

    protected function createNewInstanceWithModuleApp($uuid, $moduleUuid, $endpoint) : ExcellTransaction
    {
        $objModuleWidgetResults = $this->getLatestModuleWidgetsByUuid($moduleUuid);

        if ($objModuleWidgetResults->Result->Count === 0)
        {
            return new ExcellTransaction(false,"Module Widget Not Found: " . $moduleUuid);
        }

        $objModuleWidget = $objModuleWidgetResults->Data->First();

        return $this->makeWidgetEndpointRequest("post", $objModuleWidget->domain, $endpoint, ["instance_uuid" => $uuid]);
    }
}
