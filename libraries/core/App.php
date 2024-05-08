<?php

namespace App\Core;

use App\Core\Commands\AppCommands;
use App\Core\Managers\UriManager;
use App\Utilities\Database;
use App\Utilities\Excell\ExcellCollection;
use App\Utilities\Command\Command;
use App\Utilities\Command\CommandCaller;
use App\Utilities\Excell\ExcellHttpModel;
use App\Utilities\Transaction\ExcellTransaction;
use App\Website\Website;
use ArgumentCountError;
use Entities\Cards\Classes\CardDomains;
use Entities\Cards\Classes\Cards;
use Entities\Cards\Models\CardModel;
use Entities\Companies\Classes\Companies;
use Entities\Companies\Classes\CompanySettings;
use Entities\Companies\Classes\Departments\Departments;
use Entities\Companies\Classes\Departments\DepartmentTicketQueues;
use Entities\Companies\Models\Departments\DepartmentModel;
use Entities\Media\Classes\Images;
use Entities\Users\Classes\UserClass;
use Entities\Users\Classes\Users;
use Entities\Users\Classes\UserSettings;
use Entities\Users\Models\UserClassModel;
use Entities\Users\Models\UserModel;
use Entities\Visitors\Classes\VisitorBrowser;
use Entities\Visitors\Models\VisitorBrowserModel;
use Error;

class App
{

    public ExcellHttpModel $objHttpRequest;
    public AppCustomPlatform $objCustomPlatform;
    public AppCustomDomain $objActiveDomain;
    public AppCustomDomain $objPublicDomain;
    public AppCustomDomain $objPortalDomain;

    private array $arEnvData = [];
    public array $objCoreData = [];
    public \stdClass $objDBs;
    public array $objTemplateData = [];

    public bool $objSslSecure = false;
    public bool $objActiveSession = false;
    private bool $whiteLabelAssigned = false;
    public bool $blnNoDomain      = false;
    public bool $blnSameDomain      = false;

    // These should migrate to
    public $rootDomain;

    public array $objAppSession = [];
    public array $objAppEntities = [];
    public array $objAppRootControllerBinding = [];
    public array $objAppBaseControllerBinding = [];
    public $strPhpUser;
    public array $objSocialMediaLinks = [];
    public array $objSocialMediaVerfy = [];
    public array $objAllowedServers = [];
    public array $objBlockedServers = [];
    public array $objCustomerAccountStatuses = [];
    public array $objCustomerAccountTypes = [];
    public array $objAcceptedCreditCards = [];
    public array $objTransactionReference = [];
    public array $objUnitedStates = [];
    public array $objAllowedExt = array(".pdf",".jpeg",".jpg",".png",".gif",".html",".map",".css",".js",".zgcss",".zgjs",".woff",".txt",".xml",".svg");
    public $strActiveExtensionRequestType = "";
    public $intActiveUserId;
    public bool $blnLoggedIn = false;
    public array $lstPortalBindings = ["account","account/admin"];
    public $strActivePortalBinding;
    public array $objWebsitePages = [];
    /** @var ExcellCollection $lstAppCommands */
    public $lstAppCommands = [];
    public \stdClass $arJavaScriptLibraries;
    public \stdClass $arCssLibraries;
    public string $objWebsiteLoginPath = "login";
    public string $strAssignedPortalTheme = "1";
    public string $strAssignedWebsiteTheme = "1";
    public $ActiveLoggedInUser;
    public bool $blnForceCommands = false;
    public bool $blnModuleCache = false;
    public bool$blnWidgetCache = false;

    public function __construct()
    {
        $this->objHttpRequest = new ExcellHttpModel();
    }

    // This loads it all
    public function load() : void
    {
        try
        {
            $this->parseAndBuildSubmittedUriRequest();
            $this->buildInboundRequestModel();
            $this->loadAppConfiguration();
            $this->loadModuleRouting();
            $this->loadCoreAndRoutingData();
            $this->startCoreSession();
            $this->assignWhiteLabel();
            $this->checkForParameterCoreFunctionRequests();
            $this->registerCoreSessionData();
            $this->checkForAllowedFileTypesAndRedirectNonSlashedUrls();
            $this->runSecurityCore();
        }
        catch(\Exception $ex)
        {
            logText(date("Y-m-d"). "_Application.Load.Error.log", $ex . PHP_EOL . trace());
        }
    }

    // This runs the app
    public function run() : void
    {
        try
        {
            $objDispacher = new AppDispatcher($this);
            $objDispacher->run($this->objHttpRequest->Uri);
        }
        catch(\Exception $ex)
        {
            logText(date("Y-m-d"). "_Application.Run.Error.log", $ex . PHP_EOL . trace());
        }
    }

    // This runs the app
    public function runCommands() : void
    {
        ini_set('memory_limit', '-1');
        set_time_limit(3000);

        try
        {
            $arCommandsFromCommandLine = $this->getCommandLineArguments();

            if ($arCommandsFromCommandLine === null)
            {
                /** @var ExcellCollection $this->lstAppCommands */
                $this->lstAppCommands->Each(function($currCommand)
                {
                    /** @var CommandCaller $currCommand */
                    if (method_exists($currCommand, "Run"))
                    {
                        $currCommand->Run($this);
                    }
                });
            }
            else
            {
                foreach($arCommandsFromCommandLine as $currArgument)
                {
                    $currCommand = $this->lstAppCommands->FindEntityByProperty("name", $currArgument);

                    if ($currCommand === null)
                    {
                        continue;
                    }

                    /** @var CommandCaller $currCommand */
                    if (method_exists($currCommand, "Run"))
                    {
                        $currCommand->Run($this);
                    }
                }
            }
        }
        catch(\Exception $ex)
        {
            logText(date("Y-m-d"). "_Application.RunCommands.Error.log", $ex . PHP_EOL . trace());
        }

    }

    protected function getCommandLineArguments() : ?array
    {
        if (empty($_SERVER['argv']))
        {
            return null;
        }

        $arCommandsFromCommandLine = $_SERVER['argv'];
        unset($arCommandsFromCommandLine[0]);

        if (empty($arCommandsFromCommandLine) || !is_array($arCommandsFromCommandLine) || count($arCommandsFromCommandLine) === 0)
        {
            return null;
        }

        static::checkForForceCommandArgument($arCommandsFromCommandLine);

        return $arCommandsFromCommandLine;
    }

    protected function checkForForceCommandArgument(&$arCommandsFromCommandLine) : void
    {
        if (in_array("force", $arCommandsFromCommandLine, true))
        {
            $this->blnForceCommands = true;

            foreach($arCommandsFromCommandLine as $currCommandLineIndex => $currCommandLinePhrase)
            {
                if (strtolower($currCommandLinePhrase) == "force")
                {
                    unset($arCommandsFromCommandLine[$currCommandLineIndex]);
                }
            }

            if (count($arCommandsFromCommandLine) === 0)
            {
                $arCommandsFromCommandLine = null;
            }
        }
    }

    //    public function ValidateHttpAuthorization() : bool
    //    {
    //        $strUsername = $_SESSION["session"]["authentication"]["username"];
    //        $strPassword = $_SESSION["session"]["authentication"]["password"];
    //
    //        if ( !empty($this->objHttpRequest->UserName) && !empty($this->objHttpRequest->Password) && ( $this->objHttpRequest->UserName != $strUsername || $this->objHttpRequest->Password != $strPassword ) )
    //        {
    //            return false;
    //        }
    //
    //        return true;
    //    }


    //------------------------------------------------ CORE LOAD

    private function parseAndBuildSubmittedUriRequest() : void
    {
        $uriManager = new UriManager(new ExcellHttpModel());
        $this->objHttpRequest = $uriManager->parse()->build()->get();
    }

    private function buildInboundRequestModel() : void
    {
        $this->objHttpRequest->HasModelData = false;

        if ( is_array($this->objHttpRequest->Params) && count($this->objHttpRequest->Params) > 0)
        {
            $this->objHttpRequest->HasModelData = true;
            $this->objHttpRequest->Data->Params = $this->objHttpRequest->Params;
        }

        switch(strtolower($this->objHttpRequest->Verb ?? ""))
        {
            case "get":
            case "delete":
                return;
        }

        $this->objHttpRequest->Data->PostData = $this->buildInboundPostData($_POST);

        if (!empty($this->objHttpRequest->Data->PostData) && is_iterable($this->objHttpRequest->Data->PostData) && count($this->objHttpRequest->Data->PostData) > 0)
        {
            $this->objHttpRequest->HasModelData = true;
        }
    }

    private function buildInboundPostData($arPostData)
    {
        $objPostDetails = file_get_contents('php://input');

        try
        {
            $objPostData = json_decode($objPostDetails, false, 512, JSON_THROW_ON_ERROR);
        }
        catch (\JsonException $e)
        {
            $objPostData = null;
        }

        if (empty($objPostData))
        {
            $objPostData = new \stdClass();
        }

        if (is_string($objPostData))
        {
            $objRequestUriParameters = explode("&", $objPostData);

            try
            {
                $postArray = buildRecursiveArrayFromQueryString($objRequestUriParameters);

                $objPostData = new \stdClass();

                foreach ($postArray as $key => $value)
                {
                    $objPostData->{$key} = $value;
                }
            }
            catch(\Exception $exception)
            {

            }
        }

        foreach($objPostData as $currKey => $currData)
        {
            if (isInteger($currData))
            {
                $objPostData->$currKey = (int)$currData;
            }
        }

        if (!empty($arPostData))
        {
            foreach($arPostData as $currKey => $currData)
            {
                if (empty($objPostData->$currKey))
                {
                    if (isInteger($currData))
                    {
                        $objPostData->$currKey = (int)$currData;
                    }
                    else
                    {
                        $objPostData->$currKey = $currData;
                    }
                }
            }
        }

        return $objPostData;
    }

    private function buildRootDomain($server) : string
    {
        if (empty($server["HTTP_HOST"]) || $server["HTTP_HOST"] === "localhost")
        {
            return "";
        }

        $domainArray = array_reverse(array_filter(explode(".", $server["HTTP_HOST"])));

        return ($domainArray[1] ?? "") . "." . $domainArray[0];
    }

    private function startCoreSession() : void
    {
        $this->objSslSecure = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? true : false;
        $this->rootDomain = $this->buildRootDomain($_SERVER);

        $domain = "";

        if (strpos($this->rootDomain, "localhost") === false) {
            $domain = '.' . $this->rootDomain;
        }

        session_set_cookie_params(0, '/', $domain);
        $this->objActiveSession = session_start();

        if ( $this->objActiveSession !== true )
        {
            $this->throwProcessException("Core session unable to start.");
        }

        $strBrowserId = $_COOKIE['instance'] ?? null;

        if (empty($_SESSION['_zgexcell'])) {
            $_SESSION['_zgexcell'] = [];
        }

        $this->objAppSession = &$_SESSION['_zgexcell'];
        $ipAddress = getIncomingIpAddress();

        $this->objAppSession["Core"]["Session"]["IpAddress"] = $ipAddress;

        if (empty($_COOKIE['instance']))
        {
            $strBrowserId = md5((getIncomingIpAddress() ?? (string) rand(1000000, 9999999) ) . "_". date("YmdHis") . rand(1000, 9999));

            $domain = $this->rootDomain;

            if (strpos($this->rootDomain, "localhost") !== false) {
                $domain = "";

            }

            $blnTestCookieSet1 = setcookie('instance', $strBrowserId, strtotime('+1 years'), '/', $domain, $this->objSslSecure, false) or die("unable to create cookie 1");
            $blnTestCookieSet2 = setcookie('ip_address', getIncomingIpAddress(), strtotime('+1 years'), '/', $domain, $this->objSslSecure, false) or die("unable to create cookie 1");
        }
    }

    private function loadCoreAndRoutingData() : void
    {
        $this->objCoreData = $this->getCoreDataFromFile("data.core");

        $this->arJavaScriptLibraries = $this->getCoreDataFromFile("libraries.js.core", true);
        $this->arCssLibraries = $this->getCoreDataFromFile("libraries.css.core", true);

        // Load ApplicationJs Routing Overrides for Module Routing
        $this->loadFileIfExists(APP_ENGINE_CORE . "routing/app.routing".XT);
        $this->loadFileIfExists(APP_ENGINE_CORE . "config/app.licenses".XT);
        (new AppCommands())->Run($this);

        $this->checkForRootControllerBinding();

        $this->strPhpUser = posix_getpwuid(posix_geteuid())['name'];
        $this->objHttpRequest->Verb = $_SERVER['REQUEST_METHOD'] ?? "GET";
    }

    public function loadFileIfExists($file_path)
    {
        if (is_file($file_path))
        {
            require($file_path);
            return true;
        }

        return false;
    }

    private function checkForRootControllerBinding() : void
    {
        foreach ($this->objAppEntities as $currModuleName => $currModule)
        {
            if (!empty($currModule["ControllerRouting"]))
            {
                foreach ( $currModule["ControllerRouting"] as $currModuleControllerName => $currModuleController)
                {
                    if (!empty($currModuleController["binding"]))
                    {
                        if (in_array("##root", $currModuleController["binding"]))
                        {
                            $this->objAppRootControllerBinding[$currModuleName] = $currModuleControllerName;
                        }

                        foreach($this->lstPortalBindings as $currPortalBinding)
                        {
                            if (in_array("##" . $currPortalBinding, $currModuleController["binding"], true))
                            {
                                $this->objAppBaseControllerBinding[$currPortalBinding][$currModuleName] = $currModuleControllerName;
                            }
                        }
                    }
                }
            }
        }
    }

    private function getCoreDataFromFile($strDataFileRequest, $blnToObject = false)
    {
        if ( !is_file(APP_CORE_DATA . $strDataFileRequest.".json") )
        {
            return null;
        }

        if ( $blnToObject === false)
        {
            $objCoreData = json_decode(file_get_contents(APP_CORE_DATA.$strDataFileRequest.".json"), true);
        }
        else
        {
            $objCoreData = json_decode(file_get_contents(APP_CORE_DATA.$strDataFileRequest.".json"));
        }

        return $objCoreData;
    }

    private function getModuleDataFromFile($strModuleUriRequest) : ?array
    {
        if ( !is_file(APP_STORAGE . "core/modules.json") )
        {
            return null;
        }

        $objCoreData = json_decode(file_get_contents(APP_STORAGE . "core/modules.json"), true);

        if ( empty($objCoreData) || ! is_array($objCoreData) )
        {
            return null;
        }

        if (empty($objCoreData[$strModuleUriRequest]))
        {
            return null;
        }

        return $objCoreData[$strModuleUriRequest];
    }

    private function checkForParameterCoreFunctionRequests() : void
    {
        foreach ( $this->objHttpRequest->ParameterCoreFunctionRequests as $currRequestParam => $currParamRequest )
        {
            if ( !empty($this->objHttpRequest->Params[$currRequestParam]) )
            {
                $this->objAppSession['Core']['CoreRequests'][$currRequestParam]['_toggle'] = true;

                foreach ( $currParamRequest as $currParamRequestLabel => $currParamRequestValue )
                {
                    if ( $this->objHttpRequest->Params[$currRequestParam] == $currParamRequestLabel )
                    {
                        $this->objAppSession['Core']['CoreRequests'][$currRequestParam][$currParamRequestLabel] = $currParamRequestValue;
                        unset($this->objHttpRequest->Params[$currRequestParam]);

                        $uriManager = new UriManager($this->objHttpRequest);
                        $this->objHttpRequest = $uriManager->build()->get();

                        $this->executeUrlRedirect($this->objCoreData["Website"]['FullUrl'].$this->objHttpRequest->PathFull);
                    }
                }
            }
        }
    }

    private function registerCoreSessionData() : void
    {
        $this->objAppSession['Core']["HttpRequest"]['PathUri'] = $this->objHttpRequest->PathUri;
        $this->objAppSession['Core']["HttpRequest"]['Uri'] = $this->objHttpRequest->Uri;
        $this->objAppSession['Core']["HttpRequest"]['RequestParamsOriginal'] = $this->objHttpRequest->RequestParamsOriginal;
        $this->objAppSession['Core']["HttpRequest"]['Params'] = $this->objHttpRequest->Params;
    }

    private function checkForAllowedFileTypesAndRedirectNonSlashedUrls() : void
    {
        foreach($this->objAllowedExt as $currAllowedExt)
        {
            if (strpos(strtolower($this->objHttpRequest->UriOriginal), $currAllowedExt, 0) !== false)
            {
                $this->strActiveExtensionRequestType = $currAllowedExt;
                break;
            }
        }

        if ( substr($this->objHttpRequest->UriOriginal,-1) != "/" || empty($this->objHttpRequest->UriOriginal) || $this->objHttpRequest->UriOriginal == "/" )
        {
            return;
        }

        if (!empty($this->strActiveExtensionRequestType))
        {
            return;
        }

        // TODO - Can we fix this??
        if (!empty($_SERVER["QUERY_STRING"]) && strpos($_SERVER["QUERY_STRING"],'zgCore_Dynamic_Request=nA0jEn8L3J68u73x3yC5uWgZ386Fu4eq1i8H1821') !== false)
        {
            return;
        }

        $strOriginalPageRequestUri = str_replace('uripagerequest=' . $this->objHttpRequest->UriOriginal . '&','', $this->objHttpRequest->Params);
        $strOriginalPageRequestUri = str_replace('uripagerequest=' . $this->objHttpRequest->UriOriginal,'', $strOriginalPageRequestUri);


        $this->objHttpRequest->UriOriginal = substr($this->objHttpRequest->UriOriginal,0,-1);

        if ( !empty($strOriginalPageRequestUri) )
        {
            $this->executeUrlRedirect($this->objCoreData["Website"]['FullUrl'] . "/". $this->objHttpRequest->UriOriginal . "?" . $strOriginalPageRequestUri);
        }

        $this->executeUrlRedirect($this->objCoreData["Website"]['FullUrl'] . "/". $this->objHttpRequest->UriOriginal);
    }

    public function staticFileRequest($objRequestUri) : bool
    {
        $blnDynamicFileType = false;

        if (!headers_sent()) {

            switch (strtolower($this->strActiveExtensionRequestType)) {
                case ".html":
                    header('Content-Type:text/html');
                    break;
                case ".js":
                    header('Content-Type:text/javascript');
                    break;
                case ".zgjs":
                    $blnDynamicFileType = true;
                    break;
                case ".css":
                    header('Content-Type:text/css');
                    break;
                case ".zgcss":
                    $blnDynamicFileType = true;
                    break;
                case ".jpeg":
                    header('Content-Type:image/jpeg');
                    break;
                case ".jpg":
                    header('Content-Type:image/jpeg');
                    break;
                case ".png":
                    header('Content-Type:image/png');
                    break;
                case ".gif":
                    header('Content-Type:image/gif');
                    break;
                case ".svg":
                    header('Content-Type:image/svg+xml');
                    break;
                case ".pdf":
                    header('Content-Type:application/pdf');
                    break;
                case ".eot":
                    header('Content-Type:application/x-font-eot');
                    break;
                case ".woff":
                    header('Content-Type:application/x-font-woff');
                    break;
                case ".map":
                    header('X-PHP-Response-Code: 404', true, 404);
                    break;
            }

            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Max-Age: 1000');
        }

        $arRequestUri = $this->objHttpRequest->Uri;
        $strRootRequest = $arRequestUri[0];
        unset($arRequestUri[0]);

        $strCoreFileRequest = implode('/', $arRequestUri);

        switch($strRootRequest)
        {
            case "uploads":
                if ( $blnDynamicFileType === false )
                {
                    $strFilePathRequest = APP_CORE . $this->objHttpRequest->PathUri;

                    if ( is_file($strFilePathRequest) )
                    {
                        echo file_get_contents($strFilePathRequest,FILE_USE_INCLUDE_PATH);
                        die;
                    }
                }
                else
                {
                    $strFilePathRequest = APP_CORE . $this->objHttpRequest->PathUri;

                    if ( ! is_file($strFilePathRequest))
                    {
                        die();
                    }

                    include($strFilePathRequest);
                }
                break;
            case "_core":
                return false;
                break;
            default:
                if ( $blnDynamicFileType === false )
                {
                    $strFilePathRequest = PUBLIC_DATA . $this->objHttpRequest->PathUri;

                    if ( is_file($strFilePathRequest) )
                    {
                        echo file_get_contents($strFilePathRequest,FILE_USE_INCLUDE_PATH);
                        die;
                    }
                }
                else
                {
                    $strFilePathRequest = PUBLIC_DATA . $this->objHttpRequest->PathUri;

                    if ( ! is_file($strFilePathRequest))
                    {
                        die();
                    }

                    include($strFilePathRequest);
                }
                break;

        }

        return false;
    }

    private function loadAppConfiguration() : void
    {
        // Load ApplicationJs Configuration
        $this->parseEnvFile();

        require APP_ENGINE_CORE . "config/app.config".XT;

        $this->objDBs = arrayToObject(require APP_ENGINE_CORE . "config/app.databases".XT);
    }

    protected function assignWhiteLabel() : void
    {
        if ($this->objHttpRequest->PathFull === "health-check") {
            return;
        }

        $assignDomain = new DomainAssignmentManager(
            $this,
            new Companies(),
            new CompanySettings(),
            new CardDomains(),
            new Cards(),
            $_SERVER);

        if ($assignDomain->assignCustomPlatform() || $assignDomain->assignDomainName()) {
            $this->objCustomPlatform = $assignDomain->getCustomPlatform();
            $this->objActiveDomain = $assignDomain->getActiveDomain();
            $this->objPublicDomain = $assignDomain->getPublicDomain();
            $this->objPortalDomain = $assignDomain->getPortalDomain();
            $this->blnNoDomain = $assignDomain->checkForLocalhost();
            $this->whiteLabelAssigned = true;
        } else {
            // No Custom Platform or Domain Matching.
            $this->blnNoDomain = true;
            if ($assignDomain->isWhiteLabelFound()) {
                $this->whiteLabelAssigned = true;
                $assignDomain->loadCustomDefaultDomainFromDatabase();
            }
        }

        if ($this->blnNoDomain === false) {
            $this->checkForNonActivePlatforms($assignDomain);
            $this->setThemes();
        }

        if ($this->blnNoDomain === true) {
            return;
        }

        $this->checkForForwardedProto();
    }

    private function checkForForwardedProto() : void
    {
        $sslScheme = $_SERVER["HTTP_X_FORWARDED_PROTO"] ?? $_SERVER["REQUEST_SCHEME"];

        if ($this->objActiveDomain->getSsl() && $sslScheme !== "https") {
            $urlPathAndParams = (!empty($this->objHttpRequest->PathFull) ? "/" . $this->objHttpRequest->PathFull : "");
            $this->executeUrlRedirect($this->objActiveDomain->getDomainFullWithSsl() . $urlPathAndParams);
        }
    }

    private function checkForNonActivePlatforms(DomainAssignmentManager $assignDomain) : void
    {
        if ($assignDomain->isInactivePlatform()) {
            $companyResult = (new Companies())->getById(0);
            $this->executeUrlRedirect(($companyResult->getData()->first()->domain_portal_ssl == 1 ? "https" : "http") . "://" .$companyResult->getData()->first()->domain_portal);
        }

        if ($assignDomain->isComingSoonPlatform()) {
            (new Website($this))->showComingSoonPage();
        }
    }

    private function setThemes() : void
    {
        $websiteThemeId = $this->objCustomPlatform->getCompanySettings()->FindEntityByValue("label","website_theme") ?? 1;
        $portalThemeId = $this->objCustomPlatform->getCompanySettings()->FindEntityByValue("label","portal_theme") ?? 1;

        $this->strAssignedWebsiteTheme = (empty($websiteThemeId->value) ? "1" : $websiteThemeId->value);
        $this->strAssignedPortalTheme = (empty($portalThemeId->value) ? "1" : $portalThemeId->value);
    }

    private function getSsl($company, $field) : string
    {
        if ($company->{$field} == 1) {
            return "https://";
        }

        return "http://";
    }

    private function loadModuleRouting() : void
    {
        if (env("MODULE_CASH") === true && is_file(APP_STORAGE . "core/modules.json")) {
            $this->objAppEntities = json_decode(file_get_contents(APP_STORAGE . "core/modules.json"),true);
            return;
        }

        $objActiveApplets = $this->buildModules();

        if (!is_dir(APP_STORAGE . "core") && !mkdir(APP_STORAGE . "core")) {
            // Expception
        }

        file_put_contents(APP_STORAGE . "core/modules.json", json_encode($objActiveApplets));

        $this->objAppEntities = $objActiveApplets;
    }

    private function buildModules()
    {
        $objModulesDir = glob(APP_HTTP_ENTITIES . "*" , GLOB_ONLYDIR);

        $objActiveAppEntities = [];

        foreach( $objModulesDir as $currModuleDir)
        {
            if ( is_file($currModuleDir . "/info/main.json"))
            {
                $objCurrentModule = json_decode(file_get_contents($currModuleDir . "/info/main.json"),true);

                $objCurrentModuleMain = [];

                foreach($objCurrentModule as $objModule)
                {
                    $objCurrentModuleMain = $objModule;
                    break;
                }

                if( !empty($objCurrentModuleMain["Routes"]) && is_array($objCurrentModuleMain["Routes"]) )
                {
                    foreach($objCurrentModuleMain["Routes"] as $strRoutUrl => $currModuleRoute)
                    {
                        $objActiveAppEntities[$strRoutUrl] = $objCurrentModuleMain;

                        unset($objActiveAppEntities[$strRoutUrl]["Routes"]);

                        $arEntityControllers = glob($currModuleDir . "/controllers/*Controller" . XT);

                        foreach($arEntityControllers as $currModuleClassPath)
                        {
                            if (is_file($currModuleClassPath))
                            {
                                $reversedClass = array_values(array_reverse(explode("/", $currModuleClassPath)));
                                $classFile = buildSnakeCaseFromPascalCase(str_replace("Controller.php", "", $reversedClass[0]));
                                $objActiveAppEntities[$strRoutUrl]["ControllerRouting"][$classFile] = $reversedClass[0];
                            }
                        }


                        $objActiveAppEntities[$strRoutUrl]["Main"]["Classes"] = $this->getClassesFromModule($objActiveAppEntities[$strRoutUrl]);
                        $objActiveAppEntities[$strRoutUrl]["Main"]["Models"] = $this->getModelsFromModule($objActiveAppEntities[$strRoutUrl]);
                        $objActiveAppEntities[$strRoutUrl]["Main"]["Commands"] = $this->getCommandsFromModule($objActiveAppEntities[$strRoutUrl]);
                        $objActiveAppEntities[$strRoutUrl]["Main"]["Folders"] = [
                            "Module" => $strRoutUrl,
                            "Classes" => "classes",
                            "Controllers" => "controllers",
                            "Models" => "models",
                            "Templates" => "templates",
                            "Views" => "views"
                        ];
                    }
                }
            }
        }

        return $objActiveAppEntities;
    }

    private function getClassesFromModule($arAppModule) : array
    {
        $arModuleClasses = [];
        $strClassesPath = APP_ENTITIES . $arAppModule["ModulePath"] . "/classes/*" . XT;
        $arModuleClassPaths = glob($strClassesPath);

        foreach($arModuleClassPaths as $currModuleClassPath)
        {
            if ( is_file($currModuleClassPath))
            {
                $classes = get_declared_classes();
                include_once $currModuleClassPath;
                $diff                             = array_reverse(array_diff(get_declared_classes(), $classes));
                $objClassInstanceName             = reset($diff);
                $objClassInstanceNameArray        = explode("\\", $objClassInstanceName);
                $currClassIndex                   = array_pop($objClassInstanceNameArray);
                $arModuleClasses[$currClassIndex] = ["name" => $objClassInstanceName];

                if ($objClassInstanceName === false)
                {
                    continue;
                }

                /** @var AppEntity $objClassInstance */
                try
                {
                    $objClassInstance = new $objClassInstanceName();

                    if (property_exists(get_class($objClassInstance), "isPrimaryModule"))
                    {
                        if ($objClassInstance->isPrimaryModule === true)
                        {
                            $arModuleClasses[$currClassIndex]["primary"] = true;
                        }
                    }
                }
                catch (Error $ex)
                {
                    // Silent exit.
                    // If we cant instantiate it, we don't have to worry about hydrating it.
                }
                catch (ArgumentCountError $ex)
                {
                    // Silent exit.
                    // If we cant instantiate it, we don't have to worry about hydrating it.
                }
            }
        }

        return array_filter($arModuleClasses);
    }

    private function getModelsFromModule($arAppModule) : array
    {
        $arModuleModels = [];
        $strModelsPath = APP_ENTITIES . $arAppModule["ModulePath"] . "/models/*Model" . XT;
        $arModuleModelPaths = glob($strModelsPath);

        foreach($arModuleModelPaths as $currModuleModelPath)
        {
            if ( is_file($currModuleModelPath))
            {
                $classes = get_declared_classes();
                include_once $currModuleModelPath;
                $diff = array_diff(get_declared_classes(), $classes);
                $strClassName = reset($diff);
                $arModuleModels[] = str_replace("Model", "", $strClassName);
            }
        }

        return array_filter($arModuleModels);
    }

    private function getCommandsFromModule($arAppModule) : array
    {
        $arModuleCommands = [];
        $strModelsPath = APP_ENTITIES . $arAppModule["ModulePath"] . "/commands/*" . XT;
        $arModuleCommandPaths = glob($strModelsPath);

        foreach($arModuleCommandPaths as $currModuleCommandPath) {
            if ( is_file($currModuleCommandPath)) {
                $classes = get_declared_classes();
                include_once $currModuleCommandPath;
                $diff                             = array_reverse(array_diff(get_declared_classes(), $classes));
                $objClassInstanceName             = reset($diff);
                $objClassInstanceNameArray        = explode("\\", $objClassInstanceName);
                $currClassIndex                   = array_pop($objClassInstanceNameArray);
                $arModuleCommands[$currClassIndex] = ["class" => $objClassInstanceName];

                if ($objClassInstanceName === false) {
                    continue;
                }

                /** @var AppEntity $objClassInstance */
                try {
                    $objClassInstance = new $objClassInstanceName();

                    if (property_exists(get_class($objClassInstance), "name")) {
                        $arModuleCommands[$currClassIndex]["name"] = $objClassInstance->name;
                    }
                }
                catch (Error $ex) {
                    // Silent exit.
                    // If we cant instantiate it, we don't have to worry about hydrating it.
                }
                catch (ArgumentCountError $ex) {
                    // Silent exit.
                    // If we cant instantiate it, we don't have to worry about hydrating it.
                }
            }
        }

        return array_filter($arModuleCommands);
    }

    public function log($strClassName, $strLogText) : void
    {
        if ($strClassName === "CardConnections") {
            logText("LoadModule.Process.log", $strLogText);
        }
    }

    private function runSecurityCore() : void
    {
        if(empty($this->objAppSession["Core"]["Session"]["Authentication"]) || !is_array($this->objAppSession["Core"]["Session"]["Authentication"])) {
            $this->objAppSession["Core"]["Session"]["Authentication"] = array(
                "username" => rand(10000,99999),
                "password" => rand(10000,99999)
            );
        }

        if ($this->checkForLoggedInUser()) {
            $this->intActiveUserId = (int) $this->objAppSession["Core"]["Account"]["Primary"];
            $this->blnLoggedIn = true;
        }
    }

    private function checkForLoggedInUser() : bool
    {
        $objActiveLogins = $this->getActiveLogins();

        if ( count($objActiveLogins) === 0 ) {
            //logText("checkForLoggedInUser.log", json_encode($this->getActiveLoginsByCookie()));
        }

        if ( count($objActiveLogins) === 0 ) {
            return false;
        }

        return true;
    }

    private function getActiveLoginsByCookie() : array
    {
        $objActiveLogins = [];

        $strBrowserCookie = $_COOKIE["instance"];
        $strBrowserIp = $this->objAppSession["Core"]["Session"]["IpAddress"];
        $objBrowserCookieResult = (new VisitorBrowser())->getWhere(["browser_cookie" => $strBrowserCookie]);

        if ($objBrowserCookieResult->result->Count === 0) {
            $objNewBrowserCookie = new VisitorBrowserModel();
            $objNewBrowserCookie->browser_cookie = $strBrowserCookie;
            $objNewBrowserCookie->browser_ip = $strBrowserIp;
            $objNewBrowserCookie->created_at = date("Y-m-d H:i:s");
            $result = (new VisitorBrowser())->createNew($objNewBrowserCookie);

            return $objActiveLogins;
        }

        $objBrowserCookie = $objBrowserCookieResult->getData()->first();

        if (!empty($objBrowserCookie->user_id) && !empty($objBrowserCookie->logged_in_at) && strtotime($objBrowserCookie->logged_in_at) > strtotime("-336 hours"))
        {
            $objLoggedInUserResult = (new Users())->getWhere(["user_id" => $objBrowserCookie->user_id], 1);

            if ($objLoggedInUserResult->result->Count === 0) {
                logText("active-logins/".date("Y-m-d").".NoActiveLoginsByCookie.Process.log", "No user id found with {$objBrowserCookie->browser_cookie} cookie. Returning null active logins list.");

                return $objActiveLogins;
            }

            $user = $objLoggedInUserResult->getData()->first();
            $objActiveLogins[] = $user;
            $this->setActiveLoggedInUser($user);

            logText("active-logins/".date("Y-m-d").".ActiveLoginsByCookie.".$objLoggedInUserResult->getData()->first()->user_id.".log", "User id {$objLoggedInUserResult->getData()->first()->user_id} found with {$objBrowserCookie->browser_cookie} cookie. Returning active login.");

            $intRandomId = rand(1000,9999);

            $this->objAppSession["Core"]["Account"]["Active"][$intRandomId] = array("user_id" => $this->ActiveLoggedInUser->user_id, "preferred_name" => $this->ActiveLoggedInUser->preferred_name, "username" => $this->ActiveLoggedInUser->username, "password" => $this->ActiveLoggedInUser->password, "start_time" => date("Y-m-d h:i:s", strtotime("now")));
            $this->objAppSession["Core"]["Account"]["Primary"] = $this->ActiveLoggedInUser->user_id;
        }

        logText("active-logins/".date("Y-m-d").".NoActiveLoginsByCookie.Process.log", "No user id found with {$objBrowserCookie->browser_cookie} cookie. Returning null active logins list.");

        return $objActiveLogins;
    }

    private function updateVisitorBrowserRecord($intLoggedInUserId) : void
    {
        $strBrowserCookie = $this->objAppSession["Core"]["Session"]["Browser"] ?? "";

        if (empty($strBrowserCookie)) {
            return;
        }

        $objVisitorBrowserResult = (new VisitorBrowser())->getWhere([["user_id" => $intLoggedInUserId, "logged_in_at" => EXCELL_NULL], ["||"], ["browser_cookie" => $strBrowserCookie]], "visitor_browser_id.DESC");

        $strBrowserIp = $this->objAppSession["Core"]["Session"]["IpAddress"];

        if ($objVisitorBrowserResult->result->Count === 0) {
            $objNewBrowserCookie = new VisitorBrowserModel();
            $objNewBrowserCookie->browser_cookie = $strBrowserCookie;
            $objNewBrowserCookie->browser_ip = $strBrowserIp;
            $objNewBrowserCookie->user_id = $intLoggedInUserId;
            $objNewBrowserCookie->created_at = date("Y-m-d H:i:s");

            $result = (new VisitorBrowser())->createNew($objNewBrowserCookie);
            return;
        }

        $objNewBrowserCookie = $objVisitorBrowserResult->getData()->first();

        $strBrowserIp = $this->objAppSession["Core"]["Session"]["IpAddress"];
        $objNewBrowserCookie->browser_ip = $strBrowserIp;
        $objNewBrowserCookie->user = $intLoggedInUserId;

        $result = (new VisitorBrowser())->update($objNewBrowserCookie);
    }

    public function checkForImpersonationUser($intLoggedInUserId) : ExcellTransaction
    {
        foreach ( $this->objAppSession["Core"]["Account"]["Active"] as $intSessionKey => $objActiveSessions) {
            if ( $intLoggedInUserId === $objActiveSessions["user_id"] && !empty($this->objAppSession["Core"]["Account"]["Active"][$intSessionKey]["impersonate"])) {
                $intReturningActiveUserId = $this->objAppSession["Core"]["Account"]["Active"][$intSessionKey]["impersonate"];
                return (new Users())->getById($intReturningActiveUserId);
            }
        }

        $objImpersonationCheckResult = new ExcellTransaction();
        $objImpersonationCheckResult->result->Success = false;

        return $objImpersonationCheckResult;
    }

    private function getActiveLogins() : array
    {
        $objActiveLogins = [];

        $instance = $_COOKIE["instance"] ?? null;
        $user = $_COOKIE["user"] ?? null;

        if (!empty($instance) && !empty($user) && $user !== "visitor" && empty($this->objAppSession["Core"]["Account"]) && !$this->attemptAutoLoginFromPublicCookies($instance, $user)) {
            return $objActiveLogins;
        }

        if (!empty($this->objAppSession["Core"]["Account"]["Primary"]) && isInteger($this->objAppSession["Core"]["Account"]["Primary"])) {
            $intLoggedInUserId = $this->objAppSession["Core"]["Account"]["Primary"];
            $objImpersonationUserCheckResult = static::checkForImpersonationUser($intLoggedInUserId);

            if ($objImpersonationUserCheckResult->result->Success === false) {
                $objActiveUser = (new Users())->getRelations(["browser"])->getWhere(["user_id" => $intLoggedInUserId], 1)->getData()->first();

                //logText("active-logins/".date("Y-m-d").".ActiveLoginsBySession.{$intLoggedInUserId}.log", "{$intLoggedInUserId} is primary in user session number " . $objActiveUser->browser);

                $objActiveLogins[] = $objActiveUser;

                $this->updateVisitorBrowserRecord($intLoggedInUserId);
                return $objActiveLogins;
            } else {
                $intImpersionationUserId = $objImpersonationUserCheckResult->getData()->first()->user_id;
                //logText("active-logins/".date("Y-m-d").".ActiveLoginsBySession.{$intImpersionationUserId}.log", "IMPERSONATION {$intImpersionationUserId} as {$intLoggedInUserId}.");
                $objImpersonationUserCheckResult->getData()->first()->user_id;
                $objActiveLogins[] = (new Users())->getRelations(["browser"])->getWhere(["user_id" => $intImpersionationUserId], 1)->getData()->first();

                $this->updateVisitorBrowserRecord($intImpersionationUserId);
                return $objActiveLogins;
            }
        }

        if(!empty($this->objAppSession["Core"]["Account"]["Active"]) && is_array($this->objAppSession["Core"]["Account"]["Active"])) {
            foreach ( $this->objAppSession["Core"]["Account"]["Active"] as $strIndex => $objData ) {
                if ( strtotime($objData["start_time"]) > strtotime("-48 hours")) {
                    $objLoggedInUser = (new Users())->getRelations(["browser"])->getWhere(["user_id" => $objData["user_id"]],1);

                    if ( $objLoggedInUser->result->Success === true) {
                        //logText("active-logins/".date("Y-m-d").".ActiveLoginsBySession.".$objData["user_id"].".log", "Found ".$objData["user_id"]." in Active login list for user and loging them in as them.");

                        foreach ($objLoggedInUser->data as $objUsers) {
                            $objActiveLogins[] = $objUsers;
                        }
                    }
                }
            }
        }

        return $objActiveLogins;
    }

    private function attemptAutoLoginFromPublicCookies($instance, $user) : bool
    {
        $objUsers = new Users();
        $userResult = $objUsers->getByUuid($user);

        if ($userResult->result->Count !== 1) {
            return false;
        }

        $user = $userResult->getData()->first();

        $objBrowserCookie = (new VisitorBrowser())->getWhere(["browser_cookie" => $instance])->getData()->first();

        if (empty($objBrowserCookie->logged_in_at) || $objBrowserCookie->logged_in_at < date("Y-m-d H:i:s", strtotime("-36 hours"))) {
            return false;
        }

        $sessionId = $objUsers->setUserLoginSessionData($user, $instance);
        $objUsers->setUserActiveCookies($sessionId);

        return true;
    }

    public function userAuthentication() : bool
    {
        if (empty($this->objHttpRequest->UserName) || empty($this->objHttpRequest->Password)) { return true; }

        $user = $this->getActiveLoggedInUser();
        $userId = "visitor";
        $instanceId = "";

        if ($user !== null && !empty($this->app->objAppSession["Core"]["Session"]["Browser"])) {
            $instanceId = $this->app->objAppSession["Core"]["Session"]["Browser"];
        } else {
            $objWhereClause = "SELECT usr.*, vb.browser_cookie FROM excell_main.user usr LEFT JOIN excell_traffic.visitor_browser vb ON vb.user_id = usr.user_id WHERE usr.sys_row_id = '{$this->objHttpRequest->UserName}' && vb.browser_cookie = '{$this->objHttpRequest->Password}' ORDER BY vb.created_on DESC;";
            $objUsers = Database::getSimple($objWhereClause,"card_id");
            $objUsers->getData()->HydrateModelData(UserModel::class, true);

            if ($objUsers->result->Count === 0) {
                return  false;
            }

            $user = $objUsers->getData()->first();
            $this->setActiveLoggedInUser($user);
            $instanceId = $user->browser_cookie;
        }

        $userId = $user->toArray(["sys_row_id"])["sys_row_id"];

        if ($this->objHttpRequest->UserName !== $userId || $this->objHttpRequest->Password !== $instanceId) {
            return false;
        }

        return true;
    }

    public function getActiveLoggedInUser($debug = false) : ?UserModel
    {
        if (empty($this->objAppSession["Core"]["Account"]["Primary"])) {
            return null;
        }

        if (!empty($this->ActiveLoggedInUser) && is_a($this->ActiveLoggedInUser, UserModel::class)) {
            if ($this->objAppSession["Core"]["Account"]["Primary"] === $this->ActiveLoggedInUser->user_id) {
                return $this->ActiveLoggedInUser;
            }
        }

        if ( !empty($this->objAppSession["Core"]["Account"]["Active"]) && is_array($this->objAppSession["Core"]["Account"]["Active"])) {
            foreach ( $this->objAppSession["Core"]["Account"]["Active"] as $objRegisteredLogins ) {
                if ($objRegisteredLogins["user_id"] === $this->objAppSession["Core"]["Account"]["Primary"]) {
                    if (!empty($objRegisteredLogins["data"])) {
                        $this->setActiveLoggedInUserFromCache($objRegisteredLogins["data"]);
                    } else {
                        $objUser = (new Users())->getFks(["user_email","user_phone"])->getById($objRegisteredLogins["user_id"])->getData()->first();

                        if ($objUser === null) {
                            return null;
                        }

                        $this->setActiveLoggedInUser($objUser);
                    }

                    return $this->ActiveLoggedInUser;
                }
            }
        }

        return null;
    }

    public function setActiveLoggedInUserFromCache(array $arUser) : void
    {
        $user = new UserModel();
        $user->Hydrate($arUser, true);

        $user->AddUnvalidatedValue("Roles", new ExcellCollection());
        $user->Roles->Load($arUser["Roles"] ?? [])->HydrateModelData(UserClassModel::class, true);

        $user->AddUnvalidatedValue("Departments", new ExcellCollection());
        $user->Departments->Load($arUser["Departments"] ?? [])->HydrateModelData(DepartmentModel::class, true);

        $this->ActiveLoggedInUser = $user;
    }

    public function setActiveLoggedInUser(UserModel &$objUser) : void
    {
        $strCardMainImage = "/_ez/images/users/defaultAvatar.jpg";
        $strCardThumbImage = "/_ez/images/users/defaultAvatar.jpg";

        $userSettings = (new UserSettings())->getByUserId($objUser->user_id)->getData();
        $objUser->AddUnvalidatedValue("__settings", $userSettings->getSettings());

        $objImageResult = (new Images())->noFks()->getWhere(["entity_id" => $objUser->user_id, "image_class" => "user-avatar", "entity_name" => "user"],"image_id.DESC");
        if ($objImageResult->result->Success === true && $objImageResult->result->Count > 0) {
            $strCardMainImage = $objImageResult->getData()->first()->url;
            $strCardThumbImage = $objImageResult->getData()->first()->thumb;
        }

        $objUser->loadRoles();
        $objUser->loadDepartments();

        $objUser->AddUnvalidatedValue("main_image", $strCardMainImage);
        $objUser->AddUnvalidatedValue("main_thumb", $strCardThumbImage);

        foreach($this->objAppSession["Core"]["Account"]["Active"] as $currKey => $currUser) {
            if ($currUser["user_id"] === $objUser->user_id) {
                $user = $objUser->ToArray();
                unset($user["password"], $user["pin"]);
                $this->objAppSession["Core"]["Account"]["Active"][$currKey]["data"] = $user;
            }
        }

        $this->ActiveLoggedInUser = $objUser;
    }

    //------------------------------------------------ HELPER METHODS
    public function executeUrlRedirect($strNewUrlLocation) : void
    {
        if (empty($strNewUrlLocation)) {
            header("Location: /login");
            exit;
        }
        header("Location: ".$strNewUrlLocation);
        exit;
    }

    public function redirectToLogin() : void
    {
        if ($this->objHttpRequest->HeaderData->RequestType === "Ajax") {
            Website::GetAjaxLogin($this);
        }

        $this->objAppSession["Core"]["Session"]["RedirectAfterLogin"] = $this->objHttpRequest->PathFull;
        $this->executeUrlRedirect(getFullPortalUrl() . "/" . $this->objWebsiteLoginPath);
    }

    public function redirectToCustomPlatformCard(CardModel $objCard, bool $accessedByVanityUrl) : void
    {
        $objCompany = new Companies();
        $companyResult = $objCompany->getById($objCard->redirect_to);

        if ($companyResult->result->Count === 0) {
            return;
        }

        $company = $companyResult->getData()->first();

        $customPlatformPublicDomain = ($company->domain_public_ssl == 0 ? 'http' : 'https') . "://" . $company->domain_public;

        $this->objAppSession["Core"]["Session"]["RedirectAfterLogin"] = $this->objHttpRequest->PathFull;
        $this->executeUrlRedirect($customPlatformPublicDomain . "/" . ($accessedByVanityUrl === true ? $objCard->card_vanity_url : $objCard->card_num));
    }

    private function throwProcessException($strMessage) : void
    {
        die($strMessage);
    }

    public function logCoreError($strMessage, $strErrorId = "General") : void
    {
        if ( !is_dir(PUBLIC_DATA . "logs") && !mkdir(PUBLIC_DATA . "logs")) {
            // Exception
        }

        if ( !is_dir(PUBLIC_DATA . "logs/core") && !mkdir(PUBLIC_DATA . "logs/core")) {
            // Exception
        }

        file_put_contents(PUBLIC_DATA."logs/core/" . date("Y-m-d") . "_error.log",date("Y-m-d H:i:s") . " - Core Error [" . $strErrorId . "]: " . $strMessage, FILE_APPEND);
    }

    public function isUserLoggedIn() : bool
    {
        return $this->blnLoggedIn;
    }

    public function isAdminUrlRequest() : bool
    {
        if (!$this->isWhiteLabelAssigned() || $this->objCustomPlatform->isSameDomain() === false && $this->isPublicWebsite()) {
            return false;
        }

        $blnMatchingUrlRequest = substr($this->objHttpRequest->PathUri ?? "", 0 , strlen($this->strActivePortalBinding ?? "")) === $this->strActivePortalBinding;

        if (!$blnMatchingUrlRequest) {
            return false;
        }

        return true;
    }

    public function isAuthorizedAdminUrlRequest() : bool
    {
        if ($this->objCustomPlatform->isSameDomain() === false && $this->isPublicWebsite()) {

            return false;
        }

        $blnMatchingUrlRequest = substr($this->objHttpRequest->PathUri, 0 , strlen($this->strActivePortalBinding)) === $this->strActivePortalBinding;

        if(!$blnMatchingUrlRequest) {
            return false;
        }

        if(!$this->blnLoggedIn) {
            return false;
        }

        return true;
    }

    public function isPost() : bool
    {
        if ( strtolower($this->objHttpRequest->Verb) != "post" ) {
            return false;
        }

        return true;
    }

    public function isGet() : bool
    {
        if ( strtolower($this->objHttpRequest->Verb) != "get" ) {
            return false;
        }

        return true;
    }

    private function parseEnvFile() : void
    {
        if (!is_file(APP_CORE . ".env")) {
            return;
        }

        $strEnvContents = file_get_contents(APP_CORE . ".env");
        $arEnvContents = explode(PHP_EOL, $strEnvContents);

        if (count($arEnvContents) === 0) {
            return;
        }

        foreach($arEnvContents as $currEnvLine) {
            if (!str_contains($currEnvLine, "=")) {
                continue;
            }

            $intCurrEqualsPosition = strpos($currEnvLine, "=");
            $strEnvLineData = substr($currEnvLine, ($intCurrEqualsPosition + 1));
            $strEnvLineLabel = substr($currEnvLine, 0, $intCurrEqualsPosition);
            $this->arEnvData[$strEnvLineLabel] = str_replace("\r", "", $strEnvLineData);
        }
    }

    public function getEnv($name)
    {
        if (empty($this->arEnvData[$name])) {
            return '';
        }

        return $this->arEnvData[$name];
    }

    public function isPortalWebsite()
    {
        if ( !empty($this->objCustomPlatform) && $this->objCustomPlatform->isSameDomain() === false && getPortalUrl() !== $this->objActiveDomain->getDomain()) {
            return false;
        }

        return true;
    }

    public function isPublicWebsite()
    {
        if ( !empty($this->objCustomPlatform) && $this->objCustomPlatform->isSameDomain() === false && getPublicUrl() !== $this->objActiveDomain->getDomain()) {
            return false;
        }

        return true;
    }

    public function setAppSession(array $session) : self
    {
        $this->objAppSession = $session;

        if (!empty($_SESSION)) {
            $_SESSION['_zgexcell'] = $session;
        }
        return $this;
    }

    public function getAppSession() : array
    {
        return $this->objAppSession;
    }

    public function setCustomPlatform(AppCustomPlatform $platform) : self
    {
        $this->objCustomPlatform = $platform;
        return $this;
    }

    public function getCustomPlatform() : ?AppCustomPlatform
    {
        return $this->objCustomPlatform ?? null;
    }

    public function getActiveDomain() : ?AppCustomDomain
    {
        return $this->objActiveDomain ?? null;
    }

    public function isWhiteLabelAssigned(): bool
    {
        return $this->whiteLabelAssigned;
    }
}
