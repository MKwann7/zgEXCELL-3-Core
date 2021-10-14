<?php

namespace App\Core;

use App\Core\Managers\UriManager;
use App\Utilities\Database;
use App\Utilities\Excell\ExcellActiveController;
use App\Utilities\Excell\ExcellCollection;
use App\Utilities\Command\Command;
use App\Utilities\Command\CommandCaller;
use App\Utilities\Excell\ExcellHttpModel;
use App\Utilities\Transaction\ExcellTransaction;
use App\Website\Website;
use ArgumentCountError;
use Entities\Cards\Models\CardModel;
use Entities\Companies\Classes\Companies;
use Entities\Companies\Classes\Departments\Departments;
use Entities\Companies\Classes\Departments\DepartmentTicketQueues;
use Entities\Companies\Classes\Departments\DepartmentUserRels;
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
    // Define all statics
    private $arEnvData = [];
    public $objCoreData = [];
    public $objDBs = [];
    public $objTemplateData = [];
    public $objHttpRequest;
    public $objSslSecure = false;
    public $objActiveSession = false;
    /** @var AppCustomPlatform $objCustomPlatform */
    public $objCustomPlatform;
    public $blnNoDomain      = false;
    public $blnSameDomain      = false;
    public $activeDomain;
    public $rootDomain;
    public $objAppSession = [];
    public $objAppEntities = [];
    public $objAppRootControllerBinding = [];
    public $objAppBaseControllerBinding = [];
    public $strPhpUser;
    public $objSocialMediaLinks = [];
    public $objSocialMediaVerfy = [];
    public $objAllowedServers = [];
    public $objBlockedServers = [];
    public $objCustomerAccountStatuses = [];
    public $objCustomerAccountTypes = [];
    public $objAcceptedCreditCards = [];
    public $objTransactionReference = [];
    public $objUnitedStates = [];
    public $objAllowedExt = array(".pdf",".jpeg",".jpg",".png",".gif",".html",".map",".css",".js",".zgcss",".zgjs",".woff",".txt",".xml",".svg");
    public $strActiveExtensionRequestType;
    public $intActiveUserId;
    public $blnLoggedIn = false;
    public $lstPortalBindings = ["account","account/admin"];
    public $strActivePortalBinding;
    public $objWebsitePages = [];
    /** @var ExcellCollection $lstAppCommands */
    public $lstAppCommands = [];
    public $arJavaScriptLibraries         = [];
    public $arCssLibraries                = [];
    public $objWebsiteLoginPath = "login";
    public $strAssignedPortalTheme = "1";
    public $strAssignedWebsiteTheme = "1";
    public $ActiveLoggedInUser;
    public $blnForceCommands = false;
    public $blnModuleCache = false;
    public $blnWidgetCache = false;

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
            $this->checkForCustomPreSecurityData();
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

        switch(strtolower($this->objHttpRequest->Verb))
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

        return $domainArray[1] . "." . $domainArray[0];
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

        $strBrowserId = $_COOKIE['instance'];
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
        $this->loadFileIfExists(AppEngineCore . "routing/app.routing".XT);
        $this->loadFileIfExists(AppEngineCore . "config/app.licenses".XT);
        $this->loadFileIfExists(AppEngineCore . "commands/app.commands".XT);

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
        if ( !is_file(AppCoreData . $strDataFileRequest.".json") )
        {
            return null;
        }

        if ( $blnToObject === false)
        {
            $objCoreData = json_decode(file_get_contents(AppCoreData.$strDataFileRequest.".json"), true);
        }
        else
        {
            $objCoreData = json_decode(file_get_contents(AppCoreData.$strDataFileRequest.".json"));
        }

        return $objCoreData;
    }

    private function getModuleDataFromFile($strModuleUriRequest) : ?array
    {
        if ( !is_file(AppStorage . "core/modules.json") )
        {
            return null;
        }

        $objCoreData = json_decode(file_get_contents(AppStorage . "core/modules.json"), true);

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

    private function checkForCustomPreSecurityData() : void
    {
        // This will check to see if there is a pre-security custom data loading request on the zgexcell site.
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

        switch (strtolower($this->strActiveExtensionRequestType))
        {
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

        $arRequestUri = $this->objHttpRequest->Uri;
        $strRootRequest = $arRequestUri[0];
        unset($arRequestUri[0]);

        $strCoreFileRequest = implode('/', $arRequestUri);

        switch($strRootRequest)
        {
            case "uploads":
                if ( $blnDynamicFileType === false )
                {
                    $strFilePathRequest = AppCore . $this->objHttpRequest->PathUri;

                    if ( is_file($strFilePathRequest) )
                    {
                        echo file_get_contents($strFilePathRequest,FILE_USE_INCLUDE_PATH);
                        die;
                    }
                }
                else
                {
                    $strFilePathRequest = AppCore . $this->objHttpRequest->PathUri;

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
                    $strFilePathRequest = PublicData . $this->objHttpRequest->PathUri;

                    if ( is_file($strFilePathRequest) )
                    {
                        echo file_get_contents($strFilePathRequest,FILE_USE_INCLUDE_PATH);
                        die;
                    }
                }
                else
                {
                    $strFilePathRequest = PublicData . $this->objHttpRequest->PathUri;

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

        require AppEngineCore . "config/app.config".XT;

        $this->objDBs            = arrayToObject(require AppEngineCore . "config/app.databases".XT);
        $this->objCustomPlatform = new AppCustomPlatform();
    }

    protected function assignWhiteLabel() : void
    {
        if (empty($_SERVER["HTTP_HOST"]) || strpos($_SERVER["HTTP_HOST"], "localhost") !== false)
        {
            $this->assignRegisteredWhiteLabelLocalhost();
            return;
        }

        if (!$this->assignRegisteredWhiteLabel())
        {
            switch($this->objCustomPlatform->getCompany()->status ?? "inactive")
            {
                case "inactive":
                case "cancelled":
                case "disabled":
                    $companyResult = (new Companies())->getById(0);
                    $this->executeUrlRedirect(($companyResult->Data->First()->domain_portal_ssl == 1 ? "https" : "http") . "://" .$companyResult->Data->First()->domain_portal);
                    break;
                default:
                    (new Website($this))->showComingSoonPage();
                    break;
            }
        }

        $websiteThemeId = $this->objCustomPlatform->getCompanySettings()->FindEntityByValue("label","website_theme");
        $portalThemeId = $this->objCustomPlatform->getCompanySettings()->FindEntityByValue("label","portal_theme");
        $this->strAssignedWebsiteTheme = (empty($websiteThemeId) ? "1" : $websiteThemeId->value);
        $this->strAssignedPortalTheme = (empty($portalThemeId) ? "1" : $portalThemeId->value);

        if ($this->blnNoDomain === true || $this->objHttpRequest->PathFull === "health-check")
        {
            return;
        }

        $sslScheme = $_SERVER["HTTP_X_FORWARDED_PROTO"] ?? $_SERVER["REQUEST_SCHEME"];

        if ($this->objCoreData["Website"]['DomainSsl'] == 1 && $sslScheme !== "https")
        {
            $urlPathAndParams = (!empty($this->objHttpRequest->PathFull) ? "/" . $this->objHttpRequest->PathFull : "");
            $this->executeUrlRedirect($this->objCoreData["Website"]['FullUrl'] . $urlPathAndParams);
        }
    }

    private function assignRegisteredWhiteLabelLocalhost() : void
    {
        $domain = $_SERVER["HTTP_HOST"];
        $this->objAppSession["Core"]["App"]["Domain"]["Web"] = $_SERVER["HTTP_HOST"];
        $this->objAppSession["Core"]["App"]["Domain"]["WebFull"] = "http://" . $domain;
        $this->objAppSession["Core"]["App"]["Domain"]["WebSSL"] = false;
        $this->objAppSession["Core"]["App"]["Domain"]["WebTitle"] = "EZ Digital Local";

        $this->objAppSession["Core"]["App"]["Domain"]["Portal"] = $_SERVER["HTTP_HOST"];
        $this->objAppSession["Core"]["App"]["Domain"]["PortalFull"] = "http://" . $domain;
        $this->objAppSession["Core"]["App"]["Domain"]["PortalSSL"] = false;
        $this->objAppSession["Core"]["App"]["Domain"]["PortalTitle"] = "EZ Digital Local";

        $this->objCoreData["Website"]['MetaTitleName'] = $this->objAppSession["Core"]["App"]["Domain"]["WebTitle"];
        $this->objCoreData["Website"]['DomainName'] = $domain;
        $this->objCoreData["Website"]['DomainSsl'] = $this->objAppSession["Core"]["App"]["Domain"]["WebSSL"];
        $this->objCoreData["Website"]['FullUrl'] = $this->objAppSession["Core"]["App"]["Domain"]["WebFull"];
        $this->objCoreData["Website"]['WhiteLabel'] = $this->objAppSession["Core"]["App"]["WhiteLabel"] ?? null;

        $this->objCoreData["Website"]['SameDomain'] = true;

        $this->blnSameDomain     = true;
        $this->objCustomPlatform = new AppCustomPlatform(env("DEFAULT_COMPANY_ID"), env("DEFAULT_COMPANY_ID"), $this->activeDomain, $this->blnSameDomain, $this->objAppSession["Core"]["App"]["Domain"], $this->objCoreData["Website"]);
    }

    private function assignRegisteredWhiteLabel() : bool
    {
        $domainWeb = $this->objAppSession["Core"]["App"]["Domain"]["Web"] ?? "";
        $domainPortal = $this->objAppSession["Core"]["App"]["Domain"]["Portal"] ?? "";
        $this->objCoreData["Website"]['SameDomain'] = false;

        if (!empty($domainWeb) && $domainWeb === $_SERVER["HTTP_HOST"])
        {
            $this->activeDomain = $domainWeb;
            $this->objCoreData["Website"]['MetaTitleName'] = $this->objAppSession["Core"]["App"]["Domain"]["WebTitle"];
            $this->objCoreData["Website"]['DomainName'] = $domainWeb;
            $this->objCoreData["Website"]['DomainSsl'] = $this->objAppSession["Core"]["App"]["Domain"]["WebSSL"];
            $this->objCoreData["Website"]['FullUrl'] = $this->objAppSession["Core"]["App"]["Domain"]["WebFull"];
            $this->objCoreData["Website"]['WhiteLabel'] = $this->objAppSession["Core"]["App"]["WhiteLabel"];

            if ($domainPortal === $_SERVER["HTTP_HOST"])
            {
                $this->objCoreData["Website"]['SameDomain'] = true;
                $this->blnSameDomain = true;
            }

            $this->objCustomPlatform = new AppCustomPlatform($this->objAppSession["Core"]["App"]["WhiteLabel"]->company_id, $this->objAppSession["Core"]["App"]["WhiteLabel"]->parent_id, $this->activeDomain, $this->blnSameDomain, $this->objAppSession["Core"]["App"]["Domain"], $this->objCoreData["Website"]);
            $this->objCustomPlatform->addCompany($this->objAppSession["Core"]["App"]["WhiteLabel"]);

            if ($this->objCustomPlatform->getCompany()->status !== "active")
            {
                return false;
            }

            return true;
        }

        if (!empty($domainPortal) && $domainPortal === $_SERVER["HTTP_HOST"])
        {
            $this->activeDomain = $domainPortal;
            $this->objCoreData["Website"]['MetaTitleName'] = $this->objAppSession["Core"]["App"]["Domain"]["PortalTitle"];
            $this->objCoreData["Website"]['DomainName'] = $domainPortal;
            $this->objCoreData["Website"]['DomainSsl'] = $this->objAppSession["Core"]["App"]["Domain"]["PortalSSL"];
            $this->objCoreData["Website"]['FullUrl'] = $this->objAppSession["Core"]["App"]["Domain"]["PortalFull"];
            $this->objCoreData["Website"]['WhiteLabel'] = $this->objAppSession["Core"]["App"]["WhiteLabel"];

            $this->objCustomPlatform = new AppCustomPlatform($this->objAppSession["Core"]["App"]["WhiteLabel"]->company_id, $this->objAppSession["Core"]["App"]["WhiteLabel"]->parent_id, $this->activeDomain, $this->blnSameDomain, $this->objAppSession["Core"]["App"]["Domain"], $this->objCoreData["Website"]);
            $this->objCustomPlatform->addCompany($this->objAppSession["Core"]["App"]["WhiteLabel"]);

            if ($this->objCustomPlatform->getCompany()->status !== "active")
            {
                return false;
            }

            return true;
        }

        $companies = new Companies();
        $companyResult = $companies->getWhere([["domain_public" => $_SERVER["HTTP_HOST"]], "OR", ["domain_portal" => $_SERVER["HTTP_HOST"]]]);

        if ($companyResult->Result->Count === 0)
        {
            $this->blnNoDomain = true;
            return true;
        }

        $company = $companyResult->Data->First();

        $this->objAppSession["Core"]["App"]["WhiteLabel"] = $company;

        $this->objAppSession["Core"]["App"]["Domain"]["Web"] = $company->domain_public;
        $this->objAppSession["Core"]["App"]["Domain"]["WebFull"] = $this->getSsl($company, "domain_public_ssl") . $company->domain_public;
        $this->objAppSession["Core"]["App"]["Domain"]["WebSSL"] = $company->domain_public_ssl;
        $this->objAppSession["Core"]["App"]["Domain"]["WebTitle"] = $company->domain_public_name;

        $this->objAppSession["Core"]["App"]["Domain"]["Portal"] = $company->domain_portal;
        $this->objAppSession["Core"]["App"]["Domain"]["PortalFull"] = $this->getSsl($company, "domain_portal_ssl") . $company->domain_portal;
        $this->objAppSession["Core"]["App"]["Domain"]["PortalSSL"] = $company->domain_portal_ssl;
        $this->objAppSession["Core"]["App"]["Domain"]["PortalTitle"] = $company->domain_portal_name;

        if ($_SERVER["HTTP_HOST"] === $company->domain_public)
        {
            $this->activeDomain = $company->domain_public;
            $this->objCoreData["Website"]["MetaTitleName"] = $company->domain_public_name;
            $this->objCoreData["Website"]['DomainName'] = $company->domain_public;
            $this->objCoreData["Website"]['DomainSsl'] = $this->objAppSession["Core"]["App"]["Domain"]["WebSSL"];
            $this->objCoreData["Website"]['FullUrl'] = $this->objAppSession["Core"]["App"]["Domain"]["WebFull"];

            if ($_SERVER["HTTP_HOST"] === $company->domain_portal)
            {
                $this->objCoreData["Website"]['SameDomain'] = true;
                $this->objAppSession["Core"]["App"]['SameDomain'] = true;
                $this->blnSameDomain = true;
            }

            $this->objCustomPlatform = new AppCustomPlatform($company->company_id, $company->parent_id, $this->activeDomain, $this->blnSameDomain, $this->objAppSession["Core"]["App"]["Domain"], $this->objCoreData["Website"]);
            $this->objCustomPlatform->addCompany($company);

            if ($this->objCustomPlatform->getCompany()->status !== "active")
            {
                return false;
            }

            return true;
        }
        elseif ($_SERVER["HTTP_HOST"] === $company->domain_portal)
        {
            $this->activeDomain = $company->domain_portal;
            $this->objCoreData["Website"]["MetaTitleName"] = $company->domain_portal_name;
            $this->objCoreData["Website"]['DomainName'] = $company->domain_portal;
            $this->objCoreData["Website"]['DomainSsl'] = $this->objAppSession["Core"]["App"]["Domain"]["PortalSSL"];
            $this->objCoreData["Website"]['FullUrl'] = $this->objAppSession["Core"]["App"]["Domain"]["PortalFull"];

            $this->objCustomPlatform = new AppCustomPlatform($company->company_id,$company->parent_id, $this->activeDomain, $this->blnSameDomain, $this->objAppSession["Core"]["App"]["Domain"], $this->objCoreData["Website"]);
            $this->objCustomPlatform->addCompany($company);

            if ($this->objCustomPlatform->getCompany()->status !== "active")
            {
                return false;
            }

            return true;
        }

        $this->blnNoDomain = true;

        return true;
    }

    private function getSsl($company, $field) : string
    {
        if ($company->{$field} == 1)
        {
            return "https://";
        }

        return "http://";
    }

    private function loadModuleRouting() : void
    {
        if ($this->blnModuleCache === true && is_file(AppStorage . "core/modules.json"))
        {
            $this->objAppEntities = json_decode(file_get_contents(AppStorage . "core/modules.json"),true);
            return;
        }

        $objActiveApplets = $this->buildModules();

        if (!is_dir(AppStorage . "core") && !mkdir(AppStorage . "core"))
        {
            // Expception
        }

        file_put_contents(AppStorage . "core/modules.json", json_encode($objActiveApplets));

        $this->objAppEntities = $objActiveApplets;
    }

    private function buildModules()
    {
        $objModulesDir = glob(AppHttpEntities . "*" , GLOB_ONLYDIR);

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
                                $classFile = buildUnderscoreLowercaseFromPascalCase(str_replace("Controller.php", "", $reversedClass[0]));
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
        $strClassesPath = AppEntities . $arAppModule["ModulePath"] . "/classes/*" . XT;
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
        $strModelsPath = AppEntities . $arAppModule["ModulePath"] . "/models/*Model" . XT;
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

        $strModelsPath = AppEntities . $arAppModule["ModulePath"] . "/commands/*" . XT;

        $arModuleCommandPaths = glob($strModelsPath);

        foreach($arModuleCommandPaths as $currModuleCommandPath)
        {
            if ( is_file($currModuleCommandPath))
            {
                $classes = get_declared_classes();
                include_once $currModuleCommandPath;
                $diff                             = array_reverse(array_diff(get_declared_classes(), $classes));
                $objClassInstanceName             = reset($diff);
                $objClassInstanceNameArray        = explode("\\", $objClassInstanceName);
                $currClassIndex                   = array_pop($objClassInstanceNameArray);
                $arModuleCommands[$currClassIndex] = ["class" => $objClassInstanceName];

                if ($objClassInstanceName === false)
                {
                    continue;
                }

                /** @var AppEntity $objClassInstance */
                try
                {
                    $objClassInstance = new $objClassInstanceName();

                    if (property_exists(get_class($objClassInstance), "name"))
                    {
                        $arModuleCommands[$currClassIndex]["name"] = $objClassInstance->name;
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

        return array_filter($arModuleCommands);
    }

    public function log($strClassName, $strLogText) : void
    {
        if ($strClassName === "CardConnections")
        {
            logText("LoadModule.Process.log", $strLogText);
        }
    }

    private function runSecurityCore() : void
    {
        if(empty($this->objAppSession["Core"]["Session"]["Authentication"]) || !is_array($this->objAppSession["Core"]["Session"]["Authentication"]))
        {
            $this->objAppSession["Core"]["Session"]["Authentication"] = array(
                "username" => rand(10000,99999),
                "password" => rand(10000,99999)
            );
        }

        if ($this->checkForLoggedInUser())
        {
            $this->intActiveUserId = (int) $this->objAppSession["Core"]["Account"]["Primary"];
            $this->blnLoggedIn = true;
        }
    }

    private function checkForLoggedInUser() : bool
    {
        $objActiveLogins = $this->getActiveLogins();

        if ( count($objActiveLogins) === 0 )
        {
            //logText("checkForLoggedInUser.log", json_encode($this->getActiveLoginsByCookie()));
        }

        if ( count($objActiveLogins) === 0 )
        {
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

        if ($objBrowserCookieResult->Result->Count === 0)
        {
            $objNewBrowserCookie = new VisitorBrowserModel();
            $objNewBrowserCookie->browser_cookie = $strBrowserCookie;
            $objNewBrowserCookie->browser_ip = $strBrowserIp;
            $objNewBrowserCookie->created_at = date("Y-m-d H:i:s");
            $result = (new VisitorBrowser())->createNew($objNewBrowserCookie);

            return $objActiveLogins;
        }

        $objBrowserCookie = $objBrowserCookieResult->Data->First();

        if (!empty($objBrowserCookie->user_id) && !empty($objBrowserCookie->logged_in_at) && strtotime($objBrowserCookie->logged_in_at) > strtotime("-336 hours"))
        {
            $objLoggedInUserResult = (new Users())->getWhere(["user_id" => $objBrowserCookie->user_id], 1);

            if ($objLoggedInUserResult->Result->Count === 0)
            {
                logText("active-logins/".date("Y-m-d").".NoActiveLoginsByCookie.Process.log", "No user id found with {$objBrowserCookie->browser_cookie} cookie. Returning null active logins list.");

                return $objActiveLogins;
            }

            $objActiveLogins[] = $objLoggedInUserResult->Data->First();
            $this->setActiveLoggedInUser($objLoggedInUserResult->Data->First());

            logText("active-logins/".date("Y-m-d").".ActiveLoginsByCookie.".$objLoggedInUserResult->Data->First()->user_id.".log", "User id {$objLoggedInUserResult->Data->First()->user_id} found with {$objBrowserCookie->browser_cookie} cookie. Returning active login.");

            $intRandomId = rand(1000,9999);

            $this->objAppSession["Core"]["Account"]["Active"][$intRandomId] = array("user_id" => $this->ActiveLoggedInUser->user_id, "preferred_name" => $this->ActiveLoggedInUser->preferred_name, "username" => $this->ActiveLoggedInUser->username, "password" => $this->ActiveLoggedInUser->password, "start_time" => date("Y-m-d h:i:s", strtotime("now")));
            $this->objAppSession["Core"]["Account"]["Primary"] = $this->ActiveLoggedInUser->user_id;
        }

        logText("active-logins/".date("Y-m-d").".NoActiveLoginsByCookie.Process.log", "No user id found with {$objBrowserCookie->browser_cookie} cookie. Returning null active logins list.");

        return $objActiveLogins;
    }

    private function updateVisitorBrowserRecord($intLoggedInUserId) : void
    {
        $strBrowserCookie = $this->objAppSession["Core"]["Session"]["Browser"];
        $objVisitorBrowserResult = (new VisitorBrowser())->getWhere([["user_id" => $intLoggedInUserId, "logged_in_at" => ExcellNull], ["||"], ["browser_cookie" => $strBrowserCookie]], "visitor_browser_id.DESC");

        $strBrowserIp = $this->objAppSession["Core"]["Session"]["IpAddress"];

        if ($objVisitorBrowserResult->Result->Count === 0)
        {
            $objNewBrowserCookie = new VisitorBrowserModel();
            $objNewBrowserCookie->browser_cookie = $strBrowserCookie;
            $objNewBrowserCookie->browser_ip = $strBrowserIp;
            $objNewBrowserCookie->user_id = $intLoggedInUserId;
            $objNewBrowserCookie->created_at = date("Y-m-d H:i:s");

            $result = (new VisitorBrowser())->createNew($objNewBrowserCookie);
            return;
        }

        $objNewBrowserCookie = $objVisitorBrowserResult->Data->First();

        $strBrowserIp = $this->objAppSession["Core"]["Session"]["IpAddress"];
        $objNewBrowserCookie->browser_ip = $strBrowserIp;
        $objNewBrowserCookie->user = $intLoggedInUserId;

        $result = (new VisitorBrowser())->update($objNewBrowserCookie);
    }

    public function checkForImpersonationUser($intLoggedInUserId) : ExcellTransaction
    {
        foreach ( $this->objAppSession["Core"]["Account"]["Active"] as $intSessionKey => $objActiveSessions)
        {
            if ( $intLoggedInUserId === $objActiveSessions["user_id"] && !empty($this->objAppSession["Core"]["Account"]["Active"][$intSessionKey]["impersonate"]))
            {
                $intReturningActiveUserId = $this->objAppSession["Core"]["Account"]["Active"][$intSessionKey]["impersonate"];
                return (new Users())->getById($intReturningActiveUserId);
            }
        }

        $objImpersonationCheckResult = new ExcellTransaction();
        $objImpersonationCheckResult->Result->Success = false;

        return $objImpersonationCheckResult;
    }

    private function getActiveLogins() : array
    {
        $objActiveLogins = [];

        $instance = $_COOKIE["instance"] ?? null;
        $user = $_COOKIE["user"] ?? null;

        if (!empty($instance) && !empty($user) && $user !== "visitor" && empty($this->objAppSession["Core"]["Account"]) && !$this->attemptAutoLoginFromPublicCookies($instance, $user))
        {
            return $objActiveLogins;
        }

        if (!empty($this->objAppSession["Core"]["Account"]["Primary"]) && isInteger($this->objAppSession["Core"]["Account"]["Primary"]))
        {
            $intLoggedInUserId = $this->objAppSession["Core"]["Account"]["Primary"];
            $objImpersonationUserCheckResult = static::checkForImpersonationUser($intLoggedInUserId);

            if ($objImpersonationUserCheckResult->Result->Success === false)
            {
                $objActiveUser = (new Users())->getRelations(["browser"])->getWhere(["user_id" => $intLoggedInUserId], 1)->Data->First();

                //logText("active-logins/".date("Y-m-d").".ActiveLoginsBySession.{$intLoggedInUserId}.log", "{$intLoggedInUserId} is primary in user session number " . $objActiveUser->browser);

                $objActiveLogins[] = $objActiveUser;

                $this->updateVisitorBrowserRecord($intLoggedInUserId);
                return $objActiveLogins;
            }
            else
            {
                $intImpersionationUserId = $objImpersonationUserCheckResult->Data->First()->user_id;
                //logText("active-logins/".date("Y-m-d").".ActiveLoginsBySession.{$intImpersionationUserId}.log", "IMPERSONATION {$intImpersionationUserId} as {$intLoggedInUserId}.");
                $objImpersonationUserCheckResult->Data->First()->user_id;
                $objActiveLogins[] = (new Users())->getRelations(["browser"])->getWhere(["user_id" => $intImpersionationUserId], 1)->Data->First();

                $this->updateVisitorBrowserRecord($intImpersionationUserId);
                return $objActiveLogins;
            }
        }

        if(!empty($this->objAppSession["Core"]["Account"]["Active"]) && is_array($this->objAppSession["Core"]["Account"]["Active"]))
        {
            foreach ( $this->objAppSession["Core"]["Account"]["Active"] as $strIndex => $objData )
            {
                if ( strtotime($objData["start_time"]) > strtotime("-48 hours"))
                {
                    $objLoggedInUser = (new Users())->getRelations(["browser"])->getWhere(["user_id" => $objData["user_id"]],1);

                    if ( $objLoggedInUser->Result->Success === true)
                    {
                        //logText("active-logins/".date("Y-m-d").".ActiveLoginsBySession.".$objData["user_id"].".log", "Found ".$objData["user_id"]." in Active login list for user and loging them in as them.");

                        foreach ($objLoggedInUser->Data as $objUsers)
                        {
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

        if ($userResult->Result->Count !== 1)
        {
            return false;
        }

        $user = $userResult->Data->First();

        $objBrowserCookie = (new VisitorBrowser())->getWhere(["browser_cookie" => $instance])->Data->First();

        if (empty($objBrowserCookie->logged_in_at) || $objBrowserCookie->logged_in_at < date("Y-m-d H:i:s", strtotime("-36 hours")))
        {
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

        if ($user !== null && !empty($this->app->objAppSession["Core"]["Session"]["Browser"]))
        {
            $instanceId = $this->app->objAppSession["Core"]["Session"]["Browser"];
        }
        else
        {
            $objWhereClause = "SELECT usr.*, vb.browser_cookie FROM excell_main.user usr LEFT JOIN excell_traffic.visitor_browser vb ON vb.user_id = usr.user_id WHERE usr.sys_row_id = '{$this->objHttpRequest->UserName}' && vb.browser_cookie = '{$this->objHttpRequest->Password}' ORDER BY vb.created_on DESC;";
            $objUsers = Database::getSimple($objWhereClause,"card_id");
            $objUsers->Data->HydrateModelData(UserModel::class, true);

            if ($objUsers->Result->Count === 0)
            {
                return  false;
            }

            $user = $objUsers->Data->First();
            $this->setActiveLoggedInUser($user);
            $instanceId = $user->browser_cookie;
        }

        $userId = $user->toArray(["sys_row_id"])["sys_row_id"];

        if ($this->objHttpRequest->UserName !== $userId || $this->objHttpRequest->Password !== $instanceId)
        {
            return false;
        }

        return true;
    }

    public function getActiveLoggedInUser($debug = false) : ?UserModel
    {
        if (empty($this->objAppSession["Core"]["Account"]["Primary"]))
        {
            return null;
        }

        if (!empty($this->ActiveLoggedInUser) && is_a($this->ActiveLoggedInUser, UserModel::class))
        {
            if ($this->objAppSession["Core"]["Account"]["Primary"] === $this->ActiveLoggedInUser->user_id)
            {
                return $this->ActiveLoggedInUser;
            }
        }

        if ( !empty($this->objAppSession["Core"]["Account"]["Active"]) && is_array($this->objAppSession["Core"]["Account"]["Active"]))
        {
            foreach ( $this->objAppSession["Core"]["Account"]["Active"] as $objRegisteredLogins )
            {
                if ($objRegisteredLogins["user_id"] === $this->objAppSession["Core"]["Account"]["Primary"])
                {
                    if (!empty($objRegisteredLogins["data"]))
                    {
                        $this->setActiveLoggedInUserFromCache($objRegisteredLogins["data"]);
                    }
                    else
                    {
                        $objUser = (new Users())->getFks(["user_email","user_phone"])->getById($objRegisteredLogins["user_id"])->Data->First();

                        if ($objUser === null)
                        {
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
        $user->Roles->Load($arUser["Roles"])->HydrateModelData(UserClassModel::class, true);

        $user->AddUnvalidatedValue("Departments", new ExcellCollection());
        $user->Departments->Load($arUser["Departments"])->HydrateModelData(DepartmentModel::class, true);

        $this->ActiveLoggedInUser = $user;
    }

    public function setActiveLoggedInUser(UserModel $objUser) : void
    {
        $strCardMainImage = "/_ez/images/users/defaultAvatar.jpg";
        $strCardThumbImage = "/_ez/images/users/defaultAvatar.jpg";

        $userSettings = (new UserSettings())->getByUserId($objUser->user_id)->Data;
        $colUsers = new ExcellCollection();
        $colUsers->Add($objUser);
        $colUsers->HydrateChildModelData("__settings", ["user_id" => "user_id"], $userSettings, false, ["label" => "value"]);

        /* @var $objUser UserModel */
        $objUser = $colUsers->First();

        $objImageResult = (new Images())->noFks()->getWhere(["entity_id" => $objUser->user_id, "image_class" => "user-avatar", "entity_name" => "user"],"image_id.DESC");
        if ($objImageResult->Result->Success === true && $objImageResult->Result->Count > 0)
        {
            $strCardMainImage = $objImageResult->Data->First()->url;
            $strCardThumbImage = $objImageResult->Data->First()->thumb;
        }

        $objUserClassResult = (new UserClass())->getFks()->getWhere(["user_id" => $objUser->user_id]);
        if ($objUserClassResult->Result->Success === true && $objUserClassResult->Result->Count > 0)
        {
            $objUser->AddUnvalidatedValue("Roles", $objUserClassResult->Data);
        }

        $companyDepartmentResult = (new Departments())->getByUserId($objUser->user_id);
        if ($companyDepartmentResult->Result->Success === true && $objUserClassResult->Result->Count > 0)
        {
            $objUser->AddUnvalidatedValue("Departments", $companyDepartmentResult->Data);

            $ticketQueues = new DepartmentTicketQueues();
            $ticketQueueResult = $ticketQueues->getByUserAndDepartmentIds($objUser->user_id, $objUser->Departments->FieldsToArray(["company_department_id"]));

            if ($ticketQueueResult->Result->Count > 0)
            {
                $objUser->Departments->Foreach(function($currDepartment) use ($ticketQueueResult)
                {
                    foreach($ticketQueueResult->Data as $currTicketQueue)
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

                $objUser->AddUnvalidatedValue("departmentTicketQueuesCount", $ticketQueueResult->Result->Count);
            }
        }

        $objUser->AddUnvalidatedValue("main_image", $strCardMainImage);
        $objUser->AddUnvalidatedValue("main_thumb", $strCardThumbImage);

        foreach($this->objAppSession["Core"]["Account"]["Active"] as $currKey => $currUser)
        {
            if ($currUser["user_id"] === $objUser->user_id)
            {
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
        header("Location: ".$strNewUrlLocation);
        exit;
    }

    public function redirectToLogin() : void
    {
        if ($this->objHttpRequest->HeaderData->RequestType === "Ajax")
        {
            Website::GetAjaxLogin($this);
        }

        $this->objAppSession["Core"]["Session"]["RedirectAfterLogin"] = $this->objHttpRequest->PathFull;
        $this->executeUrlRedirect(getFullPortalUrl() . "/" . $this->objWebsiteLoginPath);
    }

    public function redirectToCustomPlatformCard(CardModel $objCard, bool $accessedByVanityUrl) : void
    {
        $objCompany = new Companies();
        $companyResult = $objCompany->getById($objCard->redirect_to);

        if ($companyResult->Result->Count === 0)
        {
            return;
        }

        $company = $companyResult->Data->First();

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
        if ( !is_dir(PublicData . "logs") && !mkdir(PublicData . "logs"))
        {
            // Exception
        }

        if ( !is_dir(PublicData . "logs/core") && !mkdir(PublicData . "logs/core"))
        {
            // Exception
        }

        file_put_contents(PublicData."logs/core/" . date("Y-m-d") . "_error.log",date("Y-m-d H:i:s") . " - Core Error [" . $strErrorId . "]: " . $strMessage, FILE_APPEND);
    }

    public function isUserLoggedIn() : bool
    {
        return $this->blnLoggedIn;
    }

    public function isAdminUrlRequest() : bool
    {
        if ($this->blnSameDomain === false && $this->isPublicWebsite())
        {
            return false;
        }

        $blnMatchingUrlRequest = substr($this->objHttpRequest->PathUri, 0 , strlen($this->strActivePortalBinding)) === $this->strActivePortalBinding;

        if (!$blnMatchingUrlRequest)
        {
            return false;
        }

        return true;
    }

    public function isAuthorizedAdminUrlRequest() : bool
    {
        if ($this->blnSameDomain === false && $this->isPublicWebsite())
        {

            return false;
        }

        $blnMatchingUrlRequest = substr($this->objHttpRequest->PathUri, 0 , strlen($this->strActivePortalBinding)) === $this->strActivePortalBinding;

        if(! $blnMatchingUrlRequest)
        {
            return false;
        }

        if(! $this->blnLoggedIn)
        {
            return false;
        }

        return true;
    }

    public function registerCommand($command_title) : CommandCaller
    {
        // Find command
        $objCommand = null;

        foreach($this->objAppEntities as $currModuleName => $currModule)
        {
            if (empty($currModule["Main"]["Commands"]) || !is_array($currModule["Main"]["Commands"]) || count($currModule["Main"]["Commands"]) === 0)
            {
                continue;
            }

            $arModuleCommands = $currModule["Main"]["Commands"];

            foreach($arModuleCommands as $currCommandName => $currCommandFileName)
            {
                $objCommandInstanceName = $currCommandFileName["name"] ?? "";

                if (strtolower($objCommandInstanceName) === strtolower($command_title))
                {
                    /** @var Command $objCommandInstance */
                    try
                    {
                        $objCommandInstance = new $currCommandFileName["class"]();

                        return new CommandCaller($command_title, $objCommandInstance);
                    }
                    catch(Error $ex)
                    {
                        return new CommandCaller($command_title, null);
                    }
                }
            }
        }

        return new CommandCaller($command_title, null);
    }

    public function isPost() : bool
    {
        if ( strtolower($this->objHttpRequest->Verb) != "post" )
        {
            return false;
        }

        return true;
    }

    public function isGet() : bool
    {
        if ( strtolower($this->objHttpRequest->Verb) != "get" )
        {
            return false;
        }

        return true;
    }

    private function parseEnvFile() : void
    {
        if (!is_file(AppCore . ".env"))
        {
            return;
        }

        $strEnvContents = file_get_contents(AppCore . ".env");
        $arEnvContents = explode(PHP_EOL, $strEnvContents);

        if (count($arEnvContents) === 0)
        {
            return;
        }

        foreach($arEnvContents as $currEnvLine)
        {
            if (strpos($currEnvLine, "=") === false)
            {
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
        if (empty($this->arEnvData[$name]))
        {
            return '';
        }

        return $this->arEnvData[$name];
    }

    public function isPortalWebsite()
    {
        if ( $this->blnSameDomain === false && getPortalUrl() !== $this->activeDomain)
        {
            return false;
        }

        return true;
    }

    public function isPublicWebsite()
    {
        if ($this->blnSameDomain === false && getPublicUrl() !== $this->activeDomain)
        {
            return false;
        }

        return true;
    }
}
