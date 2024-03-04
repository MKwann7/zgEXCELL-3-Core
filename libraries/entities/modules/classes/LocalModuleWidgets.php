<?php

namespace Entities\Modules\Classes;

use App\Core\App;
use App\Website\Vue\Classes\Base\VueComponent;
use App\website\Vue\Classes\VueHub;
use ArgumentCountError;

class LocalModuleWidgets
{
    protected $widgets = [];

    public function __construct ($debug = false)
    {
        $this->loadStaticWidgets($debug);
    }

    protected function loadStaticWidgets($debug) : void
    {
        /** @var App $app */
        global $app;

        if ($app->getEnv("APP_ENV") !== "local" && $app->blnWidgetCache === true && is_file(APP_STORAGE . "core/widgets.json"))
        {
            $this->widgets = $this->loadWidgetsByClassName(json_decode(file_get_contents(APP_STORAGE . "core/widgets.json"),true));
            return;
        }

        $objWidgets = $this->parseVueWidgetClasses($debug);

        file_put_contents(APP_STORAGE . "core/widgets.json", json_encode($objWidgets));

        $this->widgets = $this->loadWidgetsByClassName($objWidgets);
        $this->widgets[VueHub::getStaticId()] = new VueHub();
    }

    private function loadWidgetsByClassName($objWidgets) : array
    {
        $objActiveAppEntities = [];

        foreach( $objWidgets as $objClassInstanceName)
        {
            $objClassInstance = new $objClassInstanceName();
            $objActiveAppEntities[$objClassInstance->getId()] = $objClassInstance;
        }

        return $objActiveAppEntities;
    }

    private function recursiveWidgetCall($directory, &$objActiveAppEntities, $debug = false) : void
    {
        $arModuleWidgetPaths = glob($directory . "/*");

        foreach ($arModuleWidgetPaths as $currModuleWidgetPath)
        {
            if (is_file($currModuleWidgetPath) && !is_dir($currModuleWidgetPath))
            {
                if ($debug === true)
                {
                    echo $currModuleWidgetPath. PHP_EOL;
                }

                [$currClassIndex, $objClassInstanceName] = getClassData($currModuleWidgetPath);

                if ($objClassInstanceName === false || strtolower(substr($objClassInstanceName, -3)) === "app" || !class_exists($objClassInstanceName))
                {
                    continue;
                }

                /** @var VueComponent $objClassInstance */
                try
                {
                    $objClassInstance = new $objClassInstanceName();

                    if (!method_exists($objClassInstance, "getId")) {
                        continue;
                    }

                    if (property_exists(get_class($objClassInstance), "isNotDynamic"))
                    {
                        if ($objClassInstance->isNotDynamic === true)
                        {
                            continue;
                        }
                    }

                    $objActiveAppEntities[$objClassInstance->getId()] = $objClassInstanceName;
                }
                catch (ArgumentCountError | \Exception  $ex)
                {
                    // Silent exit.
                    // If we cant instantiate it, we don't have to worry about hydrating it.
                }
            }
            elseif (is_dir($currModuleWidgetPath))
            {
                $this->recursiveWidgetCall($currModuleWidgetPath, $objActiveAppEntities, $debug);
            }
        }
    }

    private function parseVueWidgetClasses($debug) : array
    {
        $objModulesDir = glob(APP_ENTITIES . "*" , GLOB_ONLYDIR);

        $objActiveAppEntities = [];

        foreach( $objModulesDir as $currModuleDir)
        {
            if (is_dir($currModuleDir . "/components/vue"))
            {
                $objVueWidgetsDir = glob($currModuleDir . "/components/vue/" . "*" , GLOB_ONLYDIR);

                foreach ($objVueWidgetsDir as $currVueWidgetDir)
                {
                    $this->recursiveWidgetCall($currVueWidgetDir, $objActiveAppEntities, $debug);
                }
            }
        }

        return $objActiveAppEntities;
    }

    public function getWidgets() : array
    {
        return $this->widgets;
    }
}