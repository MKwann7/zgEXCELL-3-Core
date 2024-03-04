<?php

namespace App\Core;

use App\Utilities\Excell\ExcellActiveController;
use App\Utilities\Excell\ExcellCollection;
use App\Utilities\Excell\ExcellHttpModel;
use App\Utilities\Transaction\ExcellTransaction;
use App\Website\Website;

class AppDispatcher
{
    /** @var App $app */
    protected $app;

    public function __construct(&$app)
    {
        $this->app = $app;
    }

    public function run(array $objRequestUri) : void
    {
        $objActiveController = $this->matchModuleControllerRequest($objRequestUri, $this->app->objAppEntities);

        if ($objActiveController->Active === true)
        {
            if (!$this->runEntityControllerRequest($objRequestUri, $objActiveController))
            {
                $this->checkForNonModuleDispatchwithUri($objRequestUri);
            }
        }
        else
        {
            $this->checkForNonModuleDispatchwithUri($objRequestUri);
        }
    }

    private function matchModuleControllerRequest($objRequestUri, $objAppEntities, $intUriIndex = 0) : ExcellActiveController
    {
        if (!empty($objRequestUri[count($objRequestUri) -1]) && strpos($objRequestUri[count($objRequestUri) -1], ".") !== false) {
            return new ExcellActiveController(false, "none", $objRequestUri[$intUriIndex]);
        }

        $objEntityIndexBinding = $this->mainOrSubControllerBinding($objAppEntities, $objRequestUri, $intUriIndex);

        if ($objEntityIndexBinding->result->Success === true)
        {
            $objMainOrSubControllerBindingCheck = $this->checkForPortalControllerBinding($objEntityIndexBinding, $objRequestUri, $objAppEntities, $intUriIndex);

            if ( $objMainOrSubControllerBindingCheck->result->Success === true)
            {
                return new ExcellActiveController(
                    true,
                    $objMainOrSubControllerBindingCheck->getData()->first()->type,
                    $objMainOrSubControllerBindingCheck->getData()->first()->module,
                    $objMainOrSubControllerBindingCheck->getData()->first()->controller,
                    $objMainOrSubControllerBindingCheck->getData()->first()->method,
                    $objMainOrSubControllerBindingCheck->getData()->first()->methodIndex
                );
            }

            $strControllerMethodRequest = $this->generateControllerMethodFromUri($objEntityIndexBinding, $objRequestUri);

            $objDefaultControllerCheck = $this->checkForDefaultControllerRequest($objEntityIndexBinding->getData()->UriControllerName, $strControllerMethodRequest, $objRequestUri, $objAppEntities, $intUriIndex);

            if ( $objDefaultControllerCheck->result->Success === true)
            {
                return new ExcellActiveController(
                    true,
                    $objDefaultControllerCheck->getData()->first()->type,
                    $objDefaultControllerCheck->getData()->first()->module,
                    $objDefaultControllerCheck->getData()->first()->controller,
                    $objDefaultControllerCheck->getData()->first()->method,
                    $objDefaultControllerCheck->getData()->first()->methodIndex
                );
            }
        }

        $strControllerRequest = "Index";
        $strControllerMethodRequest = "index";

        if (!empty($objRequestUri[$intUriIndex + 1]))
        {
            $strControllerRequest = buildControllerNameFromUri($objRequestUri[$intUriIndex + 1]);
        }

        if (!empty($objRequestUri[$intUriIndex + 2]))
        {
            $strControllerMethodRequest = buildControllerNameFromUri($objRequestUri[$intUriIndex + 2]);
        }

        $objDefaultControllerCheck = $this->checkForDefaultControllerRequest($strControllerRequest, $strControllerMethodRequest, $objRequestUri, $objAppEntities, $intUriIndex);

        if ( $objDefaultControllerCheck->result->Success === true)
        {
            return new ExcellActiveController(
                true,
                $objDefaultControllerCheck->getData()->first()->type,
                $objDefaultControllerCheck->getData()->first()->module,
                $objDefaultControllerCheck->getData()->first()->controller,
                $objDefaultControllerCheck->getData()->first()->method,
                $objDefaultControllerCheck->getData()->first()->methodIndex
            );
        }

        $objBaseBindingCheck = $this->checkForBaseBinding($strControllerMethodRequest, $strControllerRequest, $objRequestUri, $objAppEntities);

        if ( $objBaseBindingCheck->result->Success === true)
        {
            return new ExcellActiveController(
                true,
                $objBaseBindingCheck->getData()->first()->type,
                $objBaseBindingCheck->getData()->first()->module,
                $objBaseBindingCheck->getData()->first()->controller,
                $objBaseBindingCheck->getData()->first()->method,
                $objBaseBindingCheck->getData()->first()->methodIndex
            );
        }

        $objRootBindingCheck = $this->checkForRootModuleBinding($strControllerRequest, $objRequestUri, $objAppEntities);

        if ( $objRootBindingCheck->result->Success === true)
        {
            return new ExcellActiveController(
                true,
                $objRootBindingCheck->getData()->first()->type,
                $objRootBindingCheck->getData()->first()->module,
                $objRootBindingCheck->getData()->first()->controller,
                $objRootBindingCheck->getData()->first()->method,
                0
            );
        }

        if (empty($objAppEntities[$objRequestUri[$intUriIndex]]))
        {
            return new ExcellActiveController(false, "none", $objRequestUri[$intUriIndex], $strControllerRequest );
        }

        return new ExcellActiveController(false, "error", $objRequestUri[$intUriIndex], $strControllerRequest);
    }

    private function generateControllerMethodFromUri($objEntityIndexBinding, $objRequestUri) : string
    {
        if ($objEntityIndexBinding->getData()->UriControllerName !== "index" && ! empty($objRequestUri[$objEntityIndexBinding->getData()->UriControllerIndex + 1]))
        {
            return buildControllerNameFromUri($objRequestUri[$objEntityIndexBinding->getData()->UriControllerIndex + 1]);
        }
        else
        {
            if ($objRequestUri[$objEntityIndexBinding->getData()->UriControllerIndex] !== "index")
            {
                return  buildControllerNameFromUri($objRequestUri[$objEntityIndexBinding->getData()->UriControllerIndex]);
            }
            else
            {
                return buildControllerNameFromUri($objRequestUri[$objEntityIndexBinding->getData()->UriControllerIndex + 1]);
            }
        }
    }

    private function mainOrSubControllerBinding($objAppEntities, $objRequestUri, $intUriIndex) : ExcellTransaction
    {
        $objContollerBinding = new ExcellTransaction();

        $intTotalRequestUriCount = count($objRequestUri);

        for($currUriIndex = 0; $currUriIndex < $intTotalRequestUriCount; $currUriIndex++)
        {
            if (!empty($objRequestUri[$intUriIndex + $currUriIndex]) && !empty($objAppEntities[$objRequestUri[$intUriIndex + $currUriIndex]]))
            {
                $objModuleRequest = $objAppEntities[$objRequestUri[$intUriIndex + $currUriIndex]];
                $strRealModule = $objAppEntities[$objRequestUri[$intUriIndex + $currUriIndex]]["Main"]["Folders"]["Module"];

                // We have an app module that matches this request
                $strBoundControllerRequest = $this->getControllerNameFromRequest($objRequestUri, $intUriIndex, $currUriIndex + 1);
                $strControllerTestAttempt = APP_HTTP_ENTITIES . $strRealModule . "/" . $objAppEntities[$strRealModule]["Main"]["Folders"]["Controllers"] . "/" . buildControllerClassFromUri($strBoundControllerRequest) . "Controller" . XT;

                if (!is_file($strControllerTestAttempt))
                {
                    $strBoundControllerRequest = "Index";
                    $strControllerTestAttempt = APP_HTTP_ENTITIES . $strRealModule . "/" . $objAppEntities[$strRealModule]["Main"]["Folders"]["Controllers"] . "/" . buildControllerClassFromUri($strBoundControllerRequest) . "Controller" . XT;
                }

                if (is_file($strControllerTestAttempt))
                {
                    if (
                        (!empty($objAppEntities[$objRequestUri[$intUriIndex + $currUriIndex]]["ControllerRouting"][$strBoundControllerRequest]) && (!empty($objAppEntities[$objRequestUri[$intUriIndex + $currUriIndex]]["Bindings"]) || !empty($objAppEntities[$objRequestUri[$intUriIndex + $currUriIndex]]["ControllerRouting"][$strBoundControllerRequest]["binding"])) ) ||
                        (!empty($objAppEntities[$objRequestUri[$intUriIndex + $currUriIndex]]["ControllerRouting"]["index"]) && (!empty($objAppEntities[$objRequestUri[$intUriIndex + $currUriIndex]]["Bindings"]) || !empty($objAppEntities[$objRequestUri[$intUriIndex + $currUriIndex]]["ControllerRouting"]["index"]["binding"])) )
                    )
                    {
                        $objUriMatch = new \stdClass();

                        $objUriMatch->UriIndex           = $intUriIndex + $currUriIndex;
                        $objUriMatch->UriName            = $objRequestUri[$intUriIndex + $currUriIndex];
                        $objUriMatch->UriControllerIndex = $intUriIndex + $currUriIndex + 1;
                        $objUriMatch->UriControllerName  = $strBoundControllerRequest;

                        $objContollerBinding->result->Success = true;
                        $objContollerBinding->result->Count   = 1;

                        $collection = new ExcellCollection();
                        $collection->Add($objUriMatch);
                        $objContollerBinding->setData($collection);

                        return $objContollerBinding;
                    }
                }
            }
        }

        $objUriMatch = new \stdClass();

        $objUriMatch->UriIndex = -1;
        $objUriMatch->UriName = '';

        $objContollerBinding->result->Success = false;
        $objContollerBinding->result->Count = 1;
        $objContollerBinding->data = $objUriMatch;

        return $objContollerBinding;
    }

    private function checkForPortalControllerBinding($objEntityIndexBinding, $objRequestUri, $objAppEntities, $intUriIndex) : ExcellTransaction
    {
        $objReturnTransaction = new ExcellTransaction();

        $intBoundModuleIndex = $objEntityIndexBinding->getData()->first()->UriIndex;
        $strBoundControllerRequest = $objEntityIndexBinding->getData()->first()->UriControllerName;
        $lstControllerBindings = $this->buildControllerBindings($strBoundControllerRequest, $objAppEntities, $objRequestUri, $intBoundModuleIndex);

        $arBindingWeight = [];

        foreach($lstControllerBindings as $currBindingKey => $currBinding)
        {
            if ( is_array($currBinding))
            {
                if (
                    $currBindingKey === $objRequestUri[$intUriIndex] &&
                    !empty($objRequestUri[$intUriIndex + 2]) &&
                    !empty($objAppEntities[$objRequestUri[$intUriIndex + 2]]) &&
                    in_array($currBindingKey, $this->app->lstPortalBindings, true)
                )
                {
                    foreach($currBinding as $currSubBindng)
                    {
                        if ( $currSubBindng === $objRequestUri[$intUriIndex + 1] )
                        {
                            $strBoundControllerMethodRequest = $this->getControllerMethodNameFromRequest($objRequestUri, $intUriIndex, $objEntityIndexBinding->getData()->UriIndex + $intUriIndex + 1);

                            $intMethodOffsetIndex = $intUriIndex + 3;

                            if ( strtolower($strBoundControllerRequest) !== "index") { $intMethodOffsetIndex++; }

                            $strBoundModule = $objRequestUri[$intBoundModuleIndex];
                            $strRealModule = $objAppEntities[$objRequestUri[$intBoundModuleIndex]]["Main"]["Folders"]["Module"];
                            $strControllerTestAttempt = APP_HTTP_ENTITIES . $strRealModule . "/" . $objAppEntities[$strBoundModule]["Main"]["Folders"]["Controllers"] . "/" . $strBoundControllerRequest . "Controller" . XT;

                            if (is_file($strControllerTestAttempt))
                            {
                                $arBindingWeight[] = array(
                                    "Weight"                        => 2,
                                    "UriName"                       => $currBindingKey . "/" . $currSubBindng,
                                    "UriIndex"                      => $intBoundModuleIndex,
                                    "BoundModule"                   => $objRequestUri[$intBoundModuleIndex],
                                    "BoundController"               => $strBoundControllerRequest,
                                    "BoundControllerMethod"         => buildControllerMethodFromUri($strBoundControllerMethodRequest),
                                    "BoundControllerMethodUriIndex" => $intUriIndex + $intMethodOffsetIndex,
                                );
                            }
                        }
                    }
                }
            }
            elseif ($currBinding === $objRequestUri[$intUriIndex] && !empty($objAppEntities[$objRequestUri[$intUriIndex + 1]]) && in_array($currBinding, $this->app->lstPortalBindings, true))
            {
                $intMethodIndex = $objEntityIndexBinding->getData()->first()->UriControllerIndex;

                if ( strtolower($objEntityIndexBinding->getData()->first()->UriControllerName) !== "index")
                {
                    $intMethodIndex++;
                }

                $strBoundModule = $objRequestUri[$intUriIndex + 1];
                $strRealModule = $objAppEntities[$objRequestUri[$intBoundModuleIndex]]["Main"]["Folders"]["Module"];
                $strBoundControllerMethodRequest = $this->getControllerMethodNameFromRequest($objRequestUri, $intUriIndex, $intMethodIndex);

                $strControllerTestAttempt = APP_HTTP_ENTITIES . $strRealModule . "/" . $objAppEntities[$strBoundModule]["Main"]["Folders"]["Controllers"] . "/" . ucwords($strBoundControllerRequest) . "Controller" . XT;

                if (is_file($strControllerTestAttempt))
                {
                    $arBindingWeight[] = array(
                        "Weight"                        => 1,
                        "UriName"                       => $currBinding,
                        "UriIndex"                      => $intBoundModuleIndex,
                        "BoundModule"                   => $strBoundModule,
                        "BoundController"               => $strBoundControllerRequest,
                        "BoundControllerMethod"         => buildControllerMethodFromUri($strBoundControllerMethodRequest),
                        "BoundControllerMethodUriIndex" => $intMethodIndex,
                    );
                }
            }
        }

        if (count($arBindingWeight) > 0)
        {
            $intBindingIndex = -1;
            $intHighestWeight = 0;

            foreach($arBindingWeight as $currBeindingIndex => $currBinding)
            {
                if ( $intHighestWeight < $currBinding["Weight"])
                {
                    $intBindingIndex = $currBeindingIndex;
                    $intHighestWeight = $currBinding["Weight"];
                }
            }

            $arHeightestWeightedBinding = $arBindingWeight[$intBindingIndex];

            $this->app->strActivePortalBinding = $arHeightestWeightedBinding["UriName"];

            $objActiveController = new \stdClass();
            $objActiveController->type = "base";
            $objActiveController->module = $objRequestUri[$arHeightestWeightedBinding["UriIndex"]];
            $objActiveController->controller = $arHeightestWeightedBinding["BoundController"];
            $objActiveController->method = $arHeightestWeightedBinding["BoundControllerMethod"];
            $objActiveController->methodIndex = $arHeightestWeightedBinding["BoundControllerMethodUriIndex"];

            $objReturnTransaction->result->Success = true;
            $objReturnTransaction->getData()->Add($objActiveController);

            return $objReturnTransaction;
        }

        $objReturnTransaction->result->Success = false;
        return $objReturnTransaction;
    }

    private function checkForDefaultControllerRequest($strControllerRequest, $strControllerMethodRequest, $objRequestUri, $objAppEntities, $intUriIndex) : ExcellTransaction
    {
        $objReturnTransaction = new ExcellTransaction();

        $strBoundControllerRequest = $this->getControllerNameFromRequest($objRequestUri, $intUriIndex,1);

        if(!empty($objRequestUri[$intUriIndex]) && !empty($objAppEntities[$objRequestUri[$intUriIndex]]))
        {
            // Check to see if request is in the routing.json collection
            if (!empty($objAppEntities[$objRequestUri[$intUriIndex]]["ControllerRouting"][$strControllerRequest]))
            {
                $blnBindingIncorrect = false;

                // Check to see if module is bound
                if (!empty($objAppEntities[$objRequestUri[$intUriIndex]]["Bindings"]) && !in_array($objRequestUri[$intUriIndex],$objAppEntities[$objRequestUri[$intUriIndex]]["Bindings"]))
                {
                    $blnBindingIncorrect = true;
                }

                // Check to see if controller is bound
                if (!empty($objAppEntities[$objRequestUri[$intUriIndex]]["ControllerRouting"][$strBoundControllerRequest]["binding"]) && !in_array($objRequestUri[$intUriIndex],$objAppEntities[$objRequestUri[$intUriIndex]]["ControllerRouting"][$strBoundControllerRequest]["binding"]))
                {
                    $blnBindingIncorrect = true;
                }

                // If not bound, Return
                if ( $blnBindingIncorrect === false )
                {
                    $objActiveController = new \stdClass();
                    $objActiveController->type = "default";
                    $objActiveController->module = $objAppEntities[$objRequestUri[$intUriIndex]]["ModulePath"];
                    $objActiveController->controller = buildPascalCaseFromUnderscoreLowercase($strControllerRequest);
                    $objActiveController->method = buildControllerMethodFromUri($strControllerMethodRequest);
                    $objActiveController->methodIndex = $intUriIndex + 2;

                    $objReturnTransaction->result->Success = true;
                    $objReturnTransaction->getData()->Add($objActiveController);

                    return $objReturnTransaction;
                }
            }

            $strControllerTestAttempt = APP_HTTP_ENTITIES . $objAppEntities[$objRequestUri[$intUriIndex]]["ModulePath"] . "/" . $objAppEntities[$objRequestUri[$intUriIndex]]["Main"]["Folders"]["Controllers"] . "/" . $strControllerRequest . "Controller" . XT;

            if (is_file($strControllerTestAttempt))
            {
                $objActiveController = new \stdClass();
                $objActiveController->type = "dynamic";
                $objActiveController->module = $objAppEntities[$objRequestUri[$intUriIndex]]["ModulePath"];
                $objActiveController->controller = buildPascalCaseFromUnderscoreLowercase($strControllerRequest);
                $objActiveController->method = buildControllerMethodFromUri($strControllerMethodRequest);
                $objActiveController->methodIndex = $intUriIndex + 2;

                $objReturnTransaction->result->Success = true;
                $objReturnTransaction->getData()->Add($objActiveController);

                return $objReturnTransaction;
            }

            $strControllerIndexTestAttempt = APP_HTTP_ENTITIES . $objAppEntities[$objRequestUri[$intUriIndex]]["ModulePath"] . "/" . $objAppEntities[$objRequestUri[$intUriIndex]]["Main"]["Folders"]["Controllers"] . "/IndexController" . XT;

            if (is_file($strControllerIndexTestAttempt))
            {
                $strFileContent = strtolower(file_get_contents($strControllerIndexTestAttempt));

                $strControllerMethodName = "function " . strtolower(buildControllerMethodFromUri($strControllerRequest));

                if (strpos($strFileContent, $strControllerMethodName) !== false)
                {
                    $objActiveController = new \stdClass();
                    $objActiveController->type = "dynamic";
                    $objActiveController->module = $objAppEntities[$objRequestUri[$intUriIndex]]["ModulePath"];
                    $objActiveController->controller = "Index";
                    $objActiveController->method = buildControllerMethodFromUri($strControllerRequest);
                    $objActiveController->methodIndex = $intUriIndex + 1;

                    $objReturnTransaction->result->Success = true;
                    $objReturnTransaction->getData()->Add($objActiveController);

                    return $objReturnTransaction;
                }
            }
        }

        $objReturnTransaction->result->Success = false;
        return $objReturnTransaction;
    }

    private function getControllerNameFromRequest($objRequestUri, $intUriIndex, $intOffset = 1) : string
    {
        $strBoundControllerRequest = "Index";

        if (!empty($objRequestUri[$intUriIndex + $intOffset]) && isset($objRequestUri[$intUriIndex + $intOffset]))
        {
            $strBoundControllerRequest = buildControllerClassFromUri($objRequestUri[$intUriIndex + $intOffset]);
        }

        return buildControllerNameFromUri($strBoundControllerRequest);
    }

    private function getControllerMethodNameFromRequest($objRequestUri, $intUriIndex, $intOffset = 1) : string
    {
        $strBoundControllerRequest = "Index";

        if (!empty($objRequestUri[$intUriIndex + $intOffset]) && isset($objRequestUri[$intUriIndex + $intOffset]))
        {
            $strBoundControllerRequest = buildControllerMethodFromUri($objRequestUri[$intUriIndex + $intOffset]);
        }

        return buildControllerMethodFromUri($strBoundControllerRequest);
    }

    private function buildControllerBindings($strBoundControllerRequest, $objAppEntities, $objRequestUri, $intUriIndex) : array
    {
        $lstControllerBindings = [];

        if (!empty($objAppEntities[$objRequestUri[$intUriIndex]]["Bindings"]))
        {
            $blnModuleBinding = true;
            $lstControllerBindings = array_merge($objAppEntities[$objRequestUri[$intUriIndex]]["Bindings"],$lstControllerBindings);
        }

        if (!empty($objAppEntities[$objRequestUri[$intUriIndex]]["ControllerRouting"][$strBoundControllerRequest]["binding"]))
        {
            $lstControllerBindings = array_merge($objAppEntities[$objRequestUri[$intUriIndex]]["ControllerRouting"][$strBoundControllerRequest]["binding"],$lstControllerBindings);
        }

        $lstCompoundControllerBindings = $this->parseControllerBinding($lstControllerBindings);

        return $lstCompoundControllerBindings;
    }

    private function parseControllerBinding($lstControllerBindings) : array
    {
        $arControllerBindings = [];
        foreach($lstControllerBindings as $currControllerKey => $objControllerData)
        {
            if (strpos($objControllerData,"/") !== false)
            {
                $arControllerBinding = explode("/", $objControllerData);
                $strControllerBindingKey = array_shift($arControllerBinding);
                $arControllerBindingChildren[] = implode("/", $arControllerBinding);
                $arControllerBindings[$strControllerBindingKey] = $this->parseControllerBinding($arControllerBindingChildren);
            }
            else
            {
                $arControllerBindings[] = $objControllerData;
            }
        }

        return $arControllerBindings;
    }

    private function checkForRootModuleBinding($strControllerRequest, $objRequestUri, $objAppEntities) : ExcellTransaction
    {
        $objReturnTransaction = new ExcellTransaction();

        $arRequestUriReverse = array_reverse($objRequestUri);
        $arRequestUriReverse[] = "index";

        if ( is_array($this->app->objAppRootControllerBinding) && count($this->app->objAppRootControllerBinding) > 0 )
        {
            foreach ($this->app->objAppRootControllerBinding as $currModuleRootName => $currModuleControllerName)
            {
                foreach($objAppEntities[$currModuleRootName]["ControllerRouting"] as $currControllerName => $currControllerRequest)
                {
                    if ( $currControllerName === "index")
                    {
                        $objActiveController = new \stdClass();
                        $objActiveController->type = "root";
                        $objActiveController->module = $currModuleRootName;
                        $objActiveController->controller = $currModuleControllerName;
                        $objActiveController->method = $currControllerName;

                        $objReturnTransaction->result->Success = true;
                        $objReturnTransaction->getData()->Add($objActiveController);

                        return $objReturnTransaction;
                    }

                    foreach($arRequestUriReverse as $currUriIndex => $currUriValue)
                    {
                        if ( $currUriValue === $currControllerName)
                        {
                            $objActiveController = new \stdClass();
                            $objActiveController->type = "root";
                            $objActiveController->module = $currModuleRootName;
                            $objActiveController->controller = $currModuleControllerName;
                            $objActiveController->method = $currControllerName;

                            $objReturnTransaction->result->Success = true;
                            $objReturnTransaction->getData()->Add($objActiveController);

                            return $objReturnTransaction;
                        }
                    }
                }
            }
        }

        $objReturnTransaction->result->Success = false;
        return $objReturnTransaction;
    }

    private function checkForBaseBinding($strControllerMethodRequest, $strControllerRequest, $objRequestUri, $objAppEntities) : ExcellTransaction
    {
        $objReturnTransaction = new ExcellTransaction();

        if ( is_array($this->app->objAppBaseControllerBinding) && count($this->app->objAppBaseControllerBinding) > 0 )
        {
            foreach ($this->app->objAppBaseControllerBinding as $currPortalBinding => $currModule)
            {
                $lstControllerBindings = $this->parseControllerBinding([$currPortalBinding]);

                if ($this->controllerBindingMatchesRequestUri($lstControllerBindings, $objRequestUri))
                {
                    foreach ($currModule as $currModuleBaseName => $currModuleControllerName)
                    {
                        foreach ($objAppEntities[$currModuleBaseName]["ControllerRouting"] as $currControllerName => $currControllerRequest)
                        {
                            if ($strControllerRequest === $currControllerName)
                            {
                                $this->app->strActivePortalBinding = $currPortalBinding;
                                $intMethodRequestRoot = substr_count($currPortalBinding, "/") + 1;

                                $objActiveController = new \stdClass();
                                $objActiveController->type = "base";
                                $objActiveController->module = $currModuleBaseName;
                                $objActiveController->controller = $currModuleControllerName;
                                $objActiveController->method = $currControllerName;
                                $objActiveController->methodIndex = $intMethodRequestRoot;

                                $objReturnTransaction->result->Success = true;
                                $objReturnTransaction->getData()->Add($objActiveController);

                                return $objReturnTransaction;
                            }
                        }
                    }
                }
            }
        }

        $objReturnTransaction->result->Success = false;
        return $objReturnTransaction;
    }

    private function controllerBindingMatchesRequestUri($lstControllerBindings, $objRequestUri, $intUriFolderIndex = 0) : bool
    {
        foreach ($lstControllerBindings as $currBindingKey => $currBinding)
        {
            if (empty($objRequestUri[$intUriFolderIndex]))
            {
                return false;
            }

            if (is_array($currBinding))
            {
                if ($objRequestUri[$intUriFolderIndex] !== $currBindingKey)
                {
                    return false;
                }

                return $this->controllerBindingMatchesRequestUri($currBinding, $objRequestUri, $intUriFolderIndex+1);
            }
            else
            {
                if ($objRequestUri[$intUriFolderIndex] !== $currBinding)
                {
                    return false;
                }
            }
        }

        return true;
    }

    private function runEntityControllerRequest($objRequestUri, ExcellActiveController $objModuleData) : bool
    {
        $strModuleName = $objModuleData->Module;

        $strActiveModule = arrayToObject($this->app->objAppEntities[$strModuleName]);

        if (empty($strActiveModule->Main->Classes))
        {
            return false;
        }

        $objAppEntity = null;
        $objAppEntityMainClass = null;

        foreach($strActiveModule->Main->Classes as $currModalClass => $currModelFileName)
        {
            if (($currModelFileName->primary ?? false) === true)
            {
                /** @var AppEntity $objAppEntity */
                $objAppEntity = new $currModelFileName->name();
                $objAppEntity->strAliasName = $strModuleName;

                if ($this->app->objHttpRequest->HasModelData == true && !$objAppEntity->validateModel($this->app->objHttpRequest))
                {
                    // If strict mode is set, throw an error. If not, it's been flagged.
                    //throw new Exception("This data being passed into this controller failed validation.");
                }

                break;
            }
        }

        if ( $objAppEntity === null )
        {
            return false;
        }

        $intControllerMethodIndex = $objModuleData->UriMethodRequestRoot;

        $strControllerRequest = $objModuleData->Controller ?? "Index";

        $strControllerMethodRequest = $objModuleData->Method;

        $this->findControllerMethodIfBaseBinding($intControllerMethodIndex, $objRequestUri);
        $objModuleData->UriMethodRequestRoot = $intControllerMethodIndex;

        if (!empty($objRequestUri[$intControllerMethodIndex]) && $objRequestUri[$intControllerMethodIndex] != "/")
        {
            $strControllerMethodRequest = buildControllerMethodFromUri($objRequestUri[$intControllerMethodIndex]);
        }

        /** @var AppController $objRequestedController */
        $objRequestedController = $objAppEntity->getController($this->app, $strActiveModule, $strControllerRequest);

        if ($objRequestedController === null)
        {
            return false;
        }

        $objRequestedController->setAppModule($objAppEntity);

        if(!method_exists($objRequestedController, $strControllerMethodRequest) && $strControllerMethodRequest !== "index")
        {

            if(!method_exists($objRequestedController, "index"))
            {
                return false;
            }

            $strControllerMethodRequest = "index";
        }

        return $this->executeControllerMethod($objRequestedController, $strControllerMethodRequest, $objModuleData, $this->app->objHttpRequest);
    }

    private function findControllerMethodIfBaseBinding(int &$intControllerMethodIndex, array $objRequestUri) : void
    {
        $lstControllerBindings = $this->parseControllerBinding($this->app->lstPortalBindings);

        $this->recursiveRequestUriCheck($intControllerMethodIndex, $lstControllerBindings, $objRequestUri);
    }

    private function recursiveRequestUriCheck(int &$intControllerMethodIndex, array $lstControllerBindings, $objRequestUri) : void
    {
        foreach ($lstControllerBindings as $controllerBindingKey => $currControllerBinding) {
            if (
                !empty($objRequestUri[$intControllerMethodIndex]) &&
                ($controllerBindingKey === $objRequestUri[$intControllerMethodIndex] || $currControllerBinding === $objRequestUri[$intControllerMethodIndex])
            ) {
                $this->app->strActivePortalBinding .= $objRequestUri[$intControllerMethodIndex] . "/";
                $intControllerMethodIndex++;
            }

            if (is_array($currControllerBinding)) {
                $this->recursiveRequestUriCheck($intControllerMethodIndex, $currControllerBinding, $objRequestUri);
            }
        }

        if (substr($this->app->strActivePortalBinding ?? "",-1) === "/")  {
            $this->app->strActivePortalBinding = substr($this->app->strActivePortalBinding,0,-1);
        }
    }

    private function executeControllerMethod(AppController $objController, string $strControllerMethod, ExcellActiveController $objModuleData, ExcellHttpModel $objHttpRequest) : bool
    {
        try
        {
            if ( method_exists($objController, $strControllerMethod)) {
                $this->writeAccessControlHeaders();
                $objHttpRequest->setBaseUri($objModuleData->UriMethodRequestRoot, buildHyphenLowercaseFromPascalCase($strControllerMethod));

                return $objController->$strControllerMethod($objHttpRequest);
            }

            $objHttpRequest->setBaseUri($objModuleData->UriMethodRequestRoot);

            return $objController->index($objHttpRequest);
        }
        catch (\Exception $exception)
        {
            // Log error to core error
            return false;
        }
    }

    private function checkForNonModuleDispatchwithUri(array $objRequestUri) : void
    {
        if ( $objRequestUri[0] === "api" ) {
            if ( !$this->runApiControllerRequest($objRequestUri) ) {
                $this->checkForNonControllerDispatchwithUri($objRequestUri);
            }
        }
        elseif ( $objRequestUri[0] === "process" ) {
            if ( !$this->runProcessControllerRequest($objRequestUri) ) {
                $this->checkForNonControllerDispatchwithUri($objRequestUri);
            }
        }
        elseif ( $objRequestUri[0] === "module-widget" ) {
            if (!$this->runModuleControllerRequest($objRequestUri)) {
                $this->checkForNonControllerDispatchwithUri($objRequestUri);
            }
        } else {
            $this->checkForNonControllerDispatchwithUri($objRequestUri);
        }
    }

    public function runProcessControllerRequest(array $objRequestUri) : bool
    {
        if (empty($objRequestUri[1])) {
            return false;
        }

        if (empty($objRequestUri[2])) {
            return false;
        }

        $strProcessModuleName = $objRequestUri[1];
        $strProcessModuleControllerName = $objRequestUri[2];

        $strProcessControllerPath = APP_CORE . "engine/process/" .  $strProcessModuleName . "/controllers/" . $strProcessModuleControllerName . XT;

        if (!is_file($strProcessControllerPath)) {
            return false;
        }

        $app = @$this->app;

        require $strProcessControllerPath;

        return true;
    }

    public function runApiControllerRequest(array $objRequestUri) : bool
    {
        if (empty($objRequestUri[1])) {
            return false;
        }

        if (empty($objRequestUri[2])) {
            return false;
        }

        [$strApiUrl, $strApiVersion, $strProcessModuleName, $strProcessModuleControllerName] = $objRequestUri;

        $objApiController = null;
        $classExists = false;

        $strProcessControllerPath = "Http\\". str_replace("-","", ucwords($strProcessModuleName)) . "\Controllers\Api\\" . ucwords($strApiVersion) . "\ApiController";
        if (empty($objRequestUri[3])) {
            $strProcessModuleControllerName = "index";
        }

        if (!empty($objRequestUri[3])) {
            $strNonRootApiControllerFileName = buildControllerNameFromUri($objRequestUri[3]);
            $strNoneRootProcessControllerPath = "Http\\". str_replace("-","", ucwords($strProcessModuleName)) . "\Controllers\Api\\" . ucwords($strApiVersion) . "\\" . buildControllerClassFromUri($strNonRootApiControllerFileName) . "Controller";

            if (class_exists($strNoneRootProcessControllerPath)) {
                $tempApiController = new $strNoneRootProcessControllerPath($this->app);
                $tempProcessModuleControllerName = buildControllerMethodFromUri((empty($objRequestUri[4])) ? "index" : $objRequestUri[4]);

                if (method_exists($tempApiController, $tempProcessModuleControllerName)) {
                    $strProcessModuleControllerName = $tempProcessModuleControllerName;
                    $objApiController = $tempApiController;
                    $strProcessControllerPath = $strNoneRootProcessControllerPath;
                }
            } else {
                if (!class_exists($strProcessControllerPath)) {
                    return false;
                }
            }
        } else {
            if (!class_exists($strProcessControllerPath)) {
                return false;
            }
        }

        $methodName = buildControllerMethodFromUri($strProcessModuleControllerName);

        if ($objApiController === null) {
            $objApiController = new $strProcessControllerPath($this->app);
            if (!method_exists($objApiController, $methodName)) {
                die("3");
                return false;
            }
        }

        $this->writeAccessControlHeaders();
        $objApiController->$methodName($this->app->objHttpRequest);

        return true;
    }

    protected function writeAccessControlHeaders() : void
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        header('Access-Control-Max-Age: 1000');
        header('Access-Control-Allow-Headers: Origin, Accept, Content-Type, Authorization, X-Requested-With, requesttype');
    }

    public function runModuleControllerRequest(array $objRequestUri) : bool
    {
        if (empty($objRequestUri[1])) {
            return false;
        }

        if (empty($objRequestUri[2])) {
            return false;
        }

        [$strApiUrl, $strProcessModuleName, $strProcesWidgetName, $strApiVersion, $strProcessModuleControllerName] = $objRequestUri;

        $strApiControllerMethodName = "index";
        $blnControllerRequest = false;
        $strProcessControllerPath = null;

        if (!empty($objRequestUri[4])) {
            $blnRootApiContollerRequest = false;
            $strApiControllerFileName = buildControllerNameFromUri($objRequestUri[4]);
            $strProcessControllerPath = "Modules\\". buildPascalCaseFromUnderscoreLowercase($strProcessModuleName) ."\\Widgets\\" . buildPascalCaseFromUnderscoreLowercase($strProcesWidgetName) . "\Controllers\\" . ucwords($strApiVersion) . "\\" . ucwords($strApiControllerFileName) . "Controller";

            if (class_exists($strProcessControllerPath)) {
                $blnControllerRequest = true;

                if (!empty($objRequestUri[5])) {
                    $strApiControllerMethodName = buildControllerMethodFromUri($objRequestUri[5]);
                }
            }
        }

        if ($blnControllerRequest === false) {
            $strProcessControllerPath = "Modules\\". buildPascalCaseFromUnderscoreLowercase($strProcessModuleName) ."\\Widgets\\" . buildPascalCaseFromUnderscoreLowercase($strProcesWidgetName) . "\Controllers\\" . ucwords($strApiVersion) . "\\IndexController";

            if (!class_exists($strProcessControllerPath)) {
                return false;
            }

            $blnControllerRequest = true;

            if (!empty($objRequestUri[4])) {
                $strApiControllerMethodName = buildControllerMethodFromUri($objRequestUri[4]);
            }
        }


        if ($blnControllerRequest === false || $strProcessControllerPath === null) {
            return false;
        }

        $objApiController = new $strProcessControllerPath($this->app);

        if (!method_exists($objApiController, $strApiControllerMethodName)) {
            return false;
        }

        return $objApiController->$strApiControllerMethodName($this->app->objHttpRequest);
    }

    private function checkForNonControllerDispatchwithUri($objRequestUri) : void
    {
        if (!$this->app->staticFileRequest($objRequestUri)) {
            $this->loadWebsitePages();
        }
    }

    public function loadWebsitePages() : void
    {
        Website::Load($this->app);
    }
}
