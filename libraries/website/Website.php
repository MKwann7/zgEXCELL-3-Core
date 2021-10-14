<?php
/**
 * SHELL _site_core Extention for zgWeb.Solutions Web.CMS.App
 */

namespace App\Website;

use App\Core\App;
use App\Core\AppEntity;
use App\Utilities\Database;
use App\Utilities\Excell\ExcellPageModel;
use App\Utilities\Excell\ExcellPageScriptModel;
use App\Utilities\Transaction\ExcellTransaction;
use App\Website\Vue\Classes\VueApp;
use App\Website\Vue\Classes\VueModal;
use Entities\Pages\Classes\Pages;
use ReflectionClass;

class Website
{
    public  $CurrentPage;
    public  $lstWebsitePages               = [];
    public  $lstWebsitePageScripts         = [];
    public  $userSettings                  = null;
    public  $objWebsiteCurrentPage         = null;
    public  $intWebsiteCurrentPageId       = null;
    public  $intWebsiteCurrentPageParentId = null;
    private $strTemplateHeader             = "";
    private $strTemplateMobileNav          = "";
    private $strTemplateMeta               = "";
    private $strTemplateFooter             = "";
    private $strTemplateInterface          = "";
    private $strPageBody                   = "";
    private $viewFolder                    = "";
    public  $lstPageStyles                 = [];
    private $strPageLeftColumn             = "";
    private $strPageView                   = "";
    private $strPageBreadcrumb             = "";
    public  $arDdrPages                    = [];
    public  $blnShow404Page                = false;
    /** @var AppEntity $AppEntity */
    public  $AppEntity;
    /** @var VueApp $VueApp */
    public  $VueApp;
    /** @var VueModal $Modal */
    public  $Modal;
    /** @var App $app */
    private $app;

    public function __construct(App $app)
    {
        $this->app = &$app;
        $this->userSettings = $app->getActiveLoggedInUser()->__settings;
    }

    private function Initialize($blnGetPages = false)
    {
        $this->CurrentPage = new ExcellPageModel();

        if ($blnGetPages)
        {
            $this->lstWebsitePages = $this->GetPages();
        }
    }

    public function InitializePortal($objActiveModule) : self
    {
        $this->CurrentPage = new ExcellPageModel();
        $this->AppEntity = $objActiveModule;

        $this->Modal = new VueModal("Loading...", 1200);

        $helloReflection = new ReflectionClass($objActiveModule);
        $this->viewFolder = (dirname($helloReflection->getFilename())) . '/../views/';
        return $this;
    }

    public static function Load(App &$app) : void
    {
        $website = new static($app);
        $website->Initialize();

        $objPathMatching = $website->UriPathMatchesWebsiteEntity();

        if ($objPathMatching->Result->Success === false)
        {
            if ($website->app->isPublicWebsite() && !empty($website->app->objCustomPlatform->getCompany()->public_domain_404_redirect))
            {
                $app->executeUrlRedirect($website->app->objCustomPlatform->getCompany()->public_domain_404_redirect);
            }

            $website->Display404Page();
        }

        $website->DisplayWebsitePage($objPathMatching);
    }

    public function GetPages()
    {
        $objAllActivePages = (new Pages())->GetAllActivePages();

        foreach ( $objAllActivePages->Data as $currKey => $currPageData)
        {
            if ( $currPageData->Data->ddr_widget != null)
            {
                $this->arDdrPages[$currPageData->Data->page_id] = $currPageData->Data->ddr_widget;
            }
        }

        return $objAllActivePages;
    }

    public function addVueApp(VueApp $vueApp) : self
    {
        $vueApp->addModal($this->Modal);
        $this->VueApp = $vueApp;
        return $this;
    }

    private function UriPathMatchesWebsiteEntity() : ExcellTransaction
    {
        $objUriMatchResult = new ExcellTransaction();

        $strUriOriginal = (empty($this->app->objHttpRequest->UriOriginal) || $this->app->objHttpRequest->UriOriginal === "/") ? "home" : $this->app->objHttpRequest->UriOriginal;

        $strWebsiteStylePageRequest = PublicData . $strUriOriginal . XT;

        if( is_file($strWebsiteStylePageRequest))
        {
            $objUriMatchResult->Result->Success = true;
            $objUriMatchResult->Result->Count = 1;
            $objUriMatchResult->Data->Add("page-css", $strWebsiteStylePageRequest);
            return $objUriMatchResult;
        }

        if (!$this->app->isPortalWebsite())
        {
            $this->blnShow404Page = true;
            $objUriMatchResult->Result->Success = false;
            $objUriMatchResult->Result->Count = 0;
            return $objUriMatchResult;
        }

        $websiteThemeId = $this->app->objCustomPlatform->refreshCompany()->getCompanySettings()->FindEntityByValue("label","website_theme");
        $strTemplateName = (empty($websiteThemeId) ? "1" : $websiteThemeId->value);

        $strStaticPageRequest = $this->getPortalOrWebsitePage($strUriOriginal, $strTemplateName);

        if( is_file($strStaticPageRequest))
        {
            $objUriMatchResult->Result->Success = true;
            $objUriMatchResult->Result->Count = 1;
            $objUriMatchResult->Data->Add("static-pages", $strStaticPageRequest);
            return $objUriMatchResult;
        }

        $this->blnShow404Page = true;

        $objUriMatchResult->Result->Success = false;
        $objUriMatchResult->Result->Count = 0;
        return $objUriMatchResult;
    }

    private function getPortalOrWebsitePage($strUriOriginal, $strTemplateName) : ?string
    {
        $strStaticPageRequest = PublicData . "website/static-pages/" . $strUriOriginal . XT;

        switch($strUriOriginal)
        {
            case "login":
            case "privacy-policy":
            case "customer-policy":
            case "cookies-policy":
            case "support":
            case "404":
            case "404-card":
            case "reset-my-password":
            case "coming-soon-portal":
            case "coming-soon-website":
            case "terms-of-service":
                $strStaticPageRequest = PublicData . "website/template/{$strTemplateName}/pages/" . $strUriOriginal . XT;
                break;
        }

        if ( !is_file($strStaticPageRequest) )
        {
            return null;
        }

        return $strStaticPageRequest;
    }

    private function DisplayWebsitePage(ExcellTransaction $objPathMatching = null) : void
    {
        $objPageRequest = $objPathMatching->Data->GetKeyByIndex(0);

        switch($objPageRequest)
        {
            case "static-pages":

                $strTemplateName = $this->getWebsiteThemeId();

                $this->BuildPageContent($strTemplateName);
                $this->BuildPageTemplate($strTemplateName);
                $this->RenderPage($strTemplateName);
                break;

            case "page-css":

                header('Content-Type:text/css');
                require $objPathMatching->Data->First();
                die();
                break;
        }

        static::Display404Page();
        die();
    }

    private function Display404Page() : void
    {
        if (!$this->app->isPortalWebsite())
        {
            $str404PageRequest = $this->getPortalOrWebsitePage("404-card", $this->getWebsiteThemeId());
        }
        else
        {
            $str404PageRequest = $this->getPortalOrWebsitePage("404", $this->getWebsiteThemeId());
        }

        // Output buffering start
        ob_start();

        require $str404PageRequest;

        // Get output buffering results
        $this->strPageBody = ob_get_clean();

        $this->BuildPageTemplate($this->getWebsiteThemeId(), false);

        $this->RenderPage($this->getWebsiteThemeId(), false);
    }

    private function getWebsiteThemeId() : string
    {
        $websiteThemeId = $this->app->objCustomPlatform->refreshCompany()->getCompanySettings()->FindEntityByValue("label","website_theme");
        return (empty($websiteThemeId) ? "1" : $websiteThemeId->value);
    }

    private function ConfigurePageFromUri($strCurrentUriRequest = "/")
    {
        if ($this->blnShow404Page == true)
        {
            // 404 page. Set 404 template criteria
            // header('X-PHP-Response-Code: 404', true, 404);
            return false;
        }

        $this->objWebsiteCurrentPage = $this->GetCurrentPageFromUri($strCurrentUriRequest);

        if ( $this->objWebsiteCurrentPage == null )
        {
            // 404 page. Set 404 template criteria
            // header('X-PHP-Response-Code: 404', true, 404);
            $this->blnShow404Page = true;
            return false;
        }

        $this->intWebsiteCurrentPageId       = $this->objWebsiteCurrentPage->page_id;
        $this->intWebsiteCurrentPageParentId = $this->objWebsiteCurrentPage->page_parent_id;

        if ( $this->objWebsiteCurrentPage->ddr_widget != null)
        {
            // TODO: Build out DDR Logic
            $this->ConfigureDdrWebpage();

            $this->ConfigureWebpageWidgets();

            $this->ApplyWebpageContentCarets();
        }
        elseif (substr($this->objWebsiteCurrentPage->type,0,7) == "dynamic")
        {
            // TODO: Build out LLP Logic
            $this->ConfigureDynamicWebpage();

            $this->ConfigureWebpageWidgets();

            $this->ApplyWebpageContentCarets();
        }
        else
        {
            $this->ConfigureWebsitePage();

            $this->ConfigureWebpageWidgets();

            $this->ApplyWebpageContentCarets();
        }
    }

    private function ConfigureWebsitePage()
    {
        $this->CurrentPage->H1Tag             = htmlentities($this->objWebsiteCurrentPage->title);
        $this->CurrentPage->BodyId            = ($this->objWebsiteCurrentPage->unique_url == "/" ? "home-page" : $this->objWebsiteCurrentPage->unique_url . "-page");
        $this->CurrentPage->BodyClass         = $this->GenerageInitialBodyClass();
        $this->CurrentPage->Meta->Title       = htmlentities($this->objWebsiteCurrentPage->meta_title);
        $this->CurrentPage->Meta->Description = htmlentities($this->objWebsiteCurrentPage->meta_description, ENT_SUBSTITUTE, 'utf-8');
        $this->CurrentPage->Meta->Keywords    = htmlentities($this->objWebsiteCurrentPage->meta_keywords, ENT_SUBSTITUTE, 'utf-8');
        $this->CurrentPage->SnipIt->Title     = htmlentities($this->objWebsiteCurrentPage->title);
        $this->CurrentPage->SnipIt->Excerpt   = htmlentities($this->objWebsiteCurrentPage->excerpt, ENT_SUBSTITUTE, 'utf-8');
        $this->CurrentPage->Columns           = $this->objWebsiteCurrentPage->columns;

        if ( !empty($this->objWebsiteCurrentPage->ChildEntities) )
        {
            $this->CurrentPage["blocks"] = $this->GeneratePageBlocks($this->objWebsiteCurrentPage->ChildEntities["PageBlocks"]);
        }
    }

    private function GenerageInitialBodyClass()
    {
        $strInitialBodyClass = "current_page_" . $this->objWebsiteCurrentPage->page_id . " " . ( $this->objWebsiteCurrentPage->columns == 2 ? "double-column" : "single-column") . " page_type_" . $this->objWebsiteCurrentPage->type;

        return $strInitialBodyClass;
    }


    private function ConfigureDdrWebpage()
    {
        // $this->objWebsiteCurrentPage->ddr_widget
    }

    private function ConfigureDynamicWebpage()
    {

    }

    private function ConfigureWebpageWidgets()
    {

    }

    private function ApplyWebpageContentCarets()
    {

    }

    private function GetCurrentPageFromUri($strCurrentUriRequest)
    {
        foreach($this->app->objWebsitePages["Pages"] as $currKey => $currData) if ( $strCurrentUriRequest == $currData->unique_url )
        {
            $objCurrentPage = (new Pages())->getById($currData->page_id);

            return $objCurrentPage["Pages"][0];
        }

        return null;
    }

    private function GeneratePageBlocks($objPageBlockList)
    {
        $arPageBlockList = array();

        foreach($objPageBlockList as $currKey => $currData)
        {
            $arPageBlockList[$currKey]["block_id"]    = $currData->page_block_id;
            $arPageBlockList[$currKey]["page_id"]     = $currData->page_id;
            $arPageBlockList[$currKey]["title"]       = htmlentities($currData->title);
            $arPageBlockList[$currKey]["description"] = htmlentities($currData->description, ENT_SUBSTITUTE, 'utf-8');
            $arPageBlockList[$currKey]["visibility"]  = $currData->visibility;

            foreach ( $currData->block_data as $currColumnKey => $currColumnData )
            {
                $arPageBlockList[$currKey]["columns"][$currColumnKey] = $currColumnData;
            }
        }

        return $arPageBlockList;
    }

    public function BuildPageTemplate(string $strTemplateName, $blnAdmin = false) : self
    {
        $strWebTheme = ($blnAdmin === false) ? PublicWebsiteTheme : PublicPortalTheme;
        $strAppTheme = ($blnAdmin === false) ? PublicPortalTheme : AppPortalTheme;

        if ($this->VueApp !== null)
        {
            $this->strPageBreadcrumb = $this->VueApp->getBreadcrumb()->renderDynamicComponentTag();
        }

        if ($this->app->isPortalWebsite())
        {
            $this->strTemplateHeader = $this->GetTemplateHeader($strTemplateName, $strWebTheme, $strAppTheme);
            $this->strTemplateFooter = $this->GetTemplateFooter($strTemplateName, $strWebTheme, $strAppTheme);
        }

        $this->strTemplateMobileNav = $this->GetTemplateMobileNavigation($strTemplateName, $strWebTheme, $strAppTheme);
        $this->strTemplateMeta = $this->GetTemplateMeta($strTemplateName, $strWebTheme, $strAppTheme);
        $this->strTemplateInterface = $this->GetTemplateInterface($strTemplateName, $strWebTheme, $strAppTheme);

        return $this;
    }

    private function BuildPageContent($strTemplateName)
    {
        ob_start();

        $strUriOriginal = (empty($this->app->objHttpRequest->UriOriginal) || $this->app->objHttpRequest->UriOriginal === "/") ? "home" : $this->app->objHttpRequest->UriOriginal;
        $strStaticPageRequest = "";

        if ($this->blnShow404Page == true)
        {
            $strStaticPageRequest = $this->getPortalOrWebsitePage($strUriOriginal, "404");
        }
        else
        {
            $strStaticPageRequest = $this->getPortalOrWebsitePage($strUriOriginal, $strTemplateName);
        }

        if ( is_file($strStaticPageRequest) )
        {
            require $strStaticPageRequest;
        }

        // Get output buffering results
        $this->strPageBody = ob_get_clean();
    }

    public function BuildPortalViewContent($strViewName, &$objAllArgs) : self
    {
        foreach( $objAllArgs as $strArgKey => $strArgObject )
        {
            if(!empty($strArgObject) && is_array($strArgObject))
            {
                foreach($strArgObject as $curGlobalVarKey => $curGlobalVarObject )
                {
                    ${$curGlobalVarKey} = $curGlobalVarObject;
                }
            }
        }

        unset($objAppEntity, $strArgKey, $strArgObject, $objAllArgs, $curGlobalVarObject, $SesRez, $curGlobalVarKey);

        $strViewNameAndPath = str_replace(".","/", $strViewName);

        $strPathToView = $this->viewFolder . strtolower($strViewNameAndPath) . "View" . XT;

        if ( !is_file($strPathToView))
        {
            // TODO - This is duplicated logic that needs to be abstracted

            $str404PageRequest = PublicData . "website/static-pages/404" . XT;

            // Output buffering start
            ob_start();

            require $str404PageRequest;

            // Get output buffering results
            $this->strPageBody = ob_get_clean();

            return $this;
        }

        // Output buffering start
        ob_start();

        require $strPathToView;

        // Get output buffering results
        $this->strPageBody = ob_get_clean();

        return $this;
    }

    private function GetTemplateMobileNavigation(string $strTemplateName, string $strWebTheme, string $strAppTheme) : string
    {
        // Output buffering start
        ob_start();

        if (is_file($strWebTheme . $strTemplateName . '/mobile.nav' . XT))
        {
            require($strWebTheme . $strTemplateName . '/mobile.nav' . XT);
        }
        elseif (is_file($strAppTheme . $strTemplateName . '/mobile.nav' . XT))
        {
            require($strAppTheme . $strTemplateName . '/mobile.nav' . XT);
        }

        // Get output buffering results
        $strTemplateElement = ob_get_clean();

        return $strTemplateElement;
    }

    private function GetTemplateHeader(string $strTemplateName, string $strWebTheme, string $strAppTheme) : string
    {
        // Output buffering start
        ob_start();

        if (is_file($strWebTheme . $strTemplateName . '/header' . XT))
        {
            require($strWebTheme . $strTemplateName . '/header' . XT);
        }
        elseif (is_file($strAppTheme . $strTemplateName . '/header' . XT))
        {
            require($strAppTheme . $strTemplateName . '/header' . XT);
        }

        // Get output buffering results
        $strTemplateElement = ob_get_clean();

        return $strTemplateElement;
    }

    private function GetTemplateMeta(string $strTemplateName, string $strWebTheme, string $strAppTheme) : string
    {
        // Output buffering start
        ob_start();

        if (is_file($strWebTheme . $strTemplateName . '/meta.data' . XT))
        {
            require($strWebTheme . $strTemplateName . '/meta.data' . XT);
        }
        elseif (is_file($strAppTheme . $strTemplateName . '/meta.data' . XT))
        {
            require($strAppTheme . $strTemplateName . '/meta.data' . XT);
        }

        // Get output buffering results
        $strTemplateElement = ob_get_clean();

        return $strTemplateElement;
    }

    private function GetTemplateFooter(string $strTemplateName, string $strWebTheme, string $strAppTheme) : string
    {
        // Output buffering start
        ob_start();

        if (is_file($strWebTheme . $strTemplateName . '/footer' . XT))
        {
            require($strWebTheme . $strTemplateName . '/footer' . XT);
        }
        elseif (is_file($strAppTheme . $strTemplateName . '/footer' . XT))
        {
            require($strAppTheme . $strTemplateName . '/footer' . XT);
        }

        // Get output buffering results
        $strTemplateElement = ob_get_clean();

        return $strTemplateElement;
    }

    private function GetTemplateInterface(string $strTemplateName, string $strWebTheme, string $strAppTheme) : string
    {
        // Output buffering start
        ob_start();

        if (is_file($strWebTheme . $strTemplateName . '/interface' . XT))
        {
            require($strWebTheme . $strTemplateName . '/interface' . XT);
        }
        elseif (is_file($strAppTheme . $strTemplateName . '/interface' . XT))
        {
            require($strAppTheme . $strTemplateName . '/interface' . XT);
        }

        // Get output buffering results
        $strTemplateElement = ob_get_clean();

        return $strTemplateElement;
    }

    public function RenderBodyClasses() : string
    {
        $bodyClasses = [];

        if ($this->userSettings->admin_portal_theme_shade !== null)
        {
            $bodyClasses[] = "theme_shade_" . $this->userSettings->admin_portal_theme_shade;
        }
        else
        {
            $bodyClasses[] = "theme_shade_light";
        }

        if (!is_array($this->CurrentPage->BodyClasses) || count($this->CurrentPage->BodyClasses) == 0)
        {
            return implode(" ", $bodyClasses);
        }

        return implode(" ", array_merge($bodyClasses, $this->CurrentPage->BodyClasses));
    }

    public function RenderTemplateScripts() : string
    {
        $strPageScripts = "";

        if (is_array($this->lstWebsitePageScripts) && count($this->lstWebsitePageScripts) > 0)
        {
            foreach($this->lstWebsitePageScripts as $currPageScriptIndex => $currPageScript)
            {
                $strPageScripts .= $this->BuildPageScript($currPageScript);
            }
        }

        return $strPageScripts;
    }

    private function BuildPageScript(ExcellPageScriptModel $objPageScript) : string
    {
        if($objPageScript->ShowScript)
        {
            $strScriptType = $objPageScript->ShowScript ?? "text/javascript";
            return '<script type="' . $strScriptType . '">' . $objPageScript->ScriptCode . '</script>'.PHP_EOL;
        }
    }

    public function RenderPage($strTemplateName, $blnAdmin = false )
    {
        $strWebTheme = ($blnAdmin === false) ? PublicWebsiteTheme : PublicPortalTheme;
        $strAppTheme = ($blnAdmin === false) ? PublicPortalTheme : AppPortalTheme;

        $this->strPageView = str_replace(array("[APP_REPLACE_VIEW]", "[APP_REPLACE_HEADER]"), array($this->strPageBody, $this->strTemplateHeader), $this->strTemplateInterface);

        if (is_file($strWebTheme . $strTemplateName . '/theme' . XT))
        {
            require($strWebTheme . $strTemplateName . '/theme' . XT);
        }
        elseif (is_file($strAppTheme . $strTemplateName . '/theme' . XT))
        {
            require($strAppTheme . $strTemplateName . '/theme' . XT);
        }

        die();
    }

    public function RenderView()
    {
        return $this->strPageBody;
    }

    public function RenderPortalComponent($strComponentFileName)
    {
        if (is_file(PublicPortalComponents . $strComponentFileName . XT))
        {
            require PublicPortalComponents . $strComponentFileName . XT;
        }
        elseif (is_file(AppPortalComponents . $strComponentFileName . XT))
        {
            require AppPortalComponents . $strComponentFileName . XT;
        }
        else
        {
            echo '<strong>The '.$strComponentFileName.' component cannot be found.</strong>';
        }
    }

    public function SetPageStyle($strBodyId, $strStyle)
    {
        $this->app->objAppSession["Website"]["Styles"]["Page"][$strBodyId] = $strStyle;
    }

    public function GetPageStyle($strBodyId)
    {
        if (!empty($this->app->objAppSession["Website"]["Styles"]["Page"][$strBodyId]))
        {
            return $this->app->objAppSession["Website"]["Styles"]["Page"][$strBodyId];
        }
    }

    public function LoadVenderForPageScripts($strBodyId, $strVenderName) : void
    {
        if (empty($strVenderName))
        {
            return;
        }

        if (!is_array($strVenderName))
        {
            if (empty($this->app->arJavaScriptLibraries->vendor->{$strVenderName}))
            {
                return;
            }

            $this->app->objAppSession["Website"]["Venders"]["Page"][$strBodyId][$strVenderName]["all"]->JS = $this->app->arJavaScriptLibraries->vendor->{$strVenderName};
        }
        else
        {
            foreach($strVenderName as $currVender => $currVenderScript)
            {
                $this->app->objAppSession["Website"]["Venders"]["Page"][$strBodyId][$currVender][$currVenderScript]->JS->{$currVenderScript} = $this->app->arJavaScriptLibraries->vendor->{$currVender}->{$currVenderScript};
            }
        }

    }

    public function LoadVendorForPageStyles($strBodyId, $strVenderName) : void
    {
        if (empty($strVenderName))
        {
            return;
        }

        if (!is_array($strVenderName))
        {
            if (empty($this->app->arCssLibraries->vendor->{$strVenderName}))
            {
                return;
            }

            $this->app->objAppSession["Website"]["Venders"]["Page"][$strBodyId][$strVenderName]["all"]->CSS = $this->app->arCssLibraries->vendor->{$strVenderName};
        }
        else
        {
            foreach($strVenderName as $currVender => $currVenderScript)
            {
                $this->app->objAppSession["Website"]["Venders"]["Page"][$strBodyId][$currVender][$currVenderScript]->CSS->{$currVenderScript} = $this->app->arCssLibraries->vendor->{$currVender}->{$currVenderScript};
            }
        }
    }

    public function GetVendorsForPage($strBodyId)
    {
        if (!empty($this->app->objAppSession["Website"]["Venders"]["Page"][$strBodyId]))
        {
            return $this->app->objAppSession["Website"]["Venders"]["Page"][$strBodyId];
        }
    }

    public static function GetAjaxLogin(App $app)
    {
        $static = new static($app);
        $static->app->objHttpRequest->UriOriginal = "components/login.form";
        $static->app->objHttpRequest->Uri = ["components","login.form"];

        $objPathMatching = $static->UriPathMatchesWebsiteEntity();

        if ($objPathMatching->Result->Success === false)
        {
            $static->Display404Page();
        }

        ob_start();

        $blnDoNotRedirect = true;

        require $objPathMatching->Data->first();

        $static->strPageBody = ob_get_clean();

        $objAjaxReturn = [
            "success" => true,
            "message" => "You must login to proceed",
            "action" => "login",
            "data" => [ "view" => base64_encode($static->strPageBody)]
        ];

        die(json_encode($objAjaxReturn));
    }

    public function showComingSoonPage()
    {
        if (!$this->app->isPortalWebsite())
        {
            $str404PageRequest = $this->getPortalOrWebsitePage("coming-soon-portal", $this->getWebsiteThemeId());
        }
        else
        {
            $str404PageRequest = $this->getPortalOrWebsitePage("coming-soon-website", $this->getWebsiteThemeId());
        }

        // Output buffering start
        ob_start();

        require $str404PageRequest;

        // Get output buffering results
        $this->strPageBody = ob_get_clean();

        //$this->BuildPageTemplate($this->getWebsiteThemeId(), false);

        //$this->RenderPage($this->getWebsiteThemeId(), false);

        die($this->strPageBody);
    }

    public function generateJavascriptForStylesheet($arActiveJavaScriptLibraries) : void
    {
        foreach($this->app->arJavaScriptLibraries->vendor as $strVenderName => $arVenderLibraries)
        {
            foreach($arVenderLibraries as $strLibraryPath => $strLibraryFileNames)
            {
                $arLibraryPaths = explode("/", $strLibraryPath);
                if( !empty($arActiveJavaScriptLibraries["vendor"][$strVenderName]) && $arActiveJavaScriptLibraries["vendor"][$strVenderName] === true)
                {
                    foreach($strLibraryFileNames as $strLibraryFileName)
                    {
                        $strJsFilePath = AppVendors . $strVenderName . "/" . $strLibraryPath . "/min/" . $strLibraryFileName;

                        if (is_file($strJsFilePath))
                        {
                            echo "/* ".$strVenderName . " " .$strLibraryPath . " " . $strLibraryFileName . " */" . PHP_EOL . PHP_EOL;
                            require $strJsFilePath;
                            echo PHP_EOL . PHP_EOL;
                        }
                    }
                }
                elseif( !empty($arActiveJavaScriptLibraries["vendor"][$strVenderName]) && is_array($arActiveJavaScriptLibraries["vendor"][$strVenderName]))
                {
                    foreach($strLibraryFileNames as $strLibraryFileName)
                    {
                        if( !empty($arActiveJavaScriptLibraries["vendor"][$strVenderName]) && !empty($arActiveJavaScriptLibraries["vendor"][$strVenderName][$arLibraryPaths[0]]) && $arActiveJavaScriptLibraries["vendor"][$strVenderName][$arLibraryPaths[0]] === true)
                        {
                            $strJsFilePath = AppVendors . $strVenderName . "/" . $strLibraryPath . "/min/" . $strLibraryFileName;

                            if (is_file($strJsFilePath))
                            {
                                echo "/* ".$strVenderName . " " .$strLibraryPath . " " . $strLibraryFileName . " */" . PHP_EOL . PHP_EOL;
                                require $strJsFilePath;
                                echo PHP_EOL . PHP_EOL;
                            }
                        }
                    }
                }
            }
        }
    }
}
