<?php

namespace App\Core;

use App\Utilities\Excell\ExcellCollection;
use Entities\Cards\Classes\CardDomains;
use Entities\Cards\Classes\Cards;
use Entities\Cards\Models\CardDomainModel;
use Entities\Companies\Classes\Companies;
use Entities\Companies\Classes\CompanySettings;
use Entities\Companies\Models\CompanyModel;

class DomainAssignmentManager
{
    protected App $app;
    protected array $appSession;
    protected AppCustomPlatform $customPlatform;
    protected AppCustomDomain $publicDomain;
    protected AppCustomDomain $portalDomain;
    protected AppCustomDomain $appDomain;

    protected Companies $companies;
    protected CompanySettings $companySettings;
    protected CardDomains $cardDomains;
    protected Cards $cards;
    protected array $server;
    protected bool $whiteLabelFound = true;

    const HTTPS_PROTOCOL = "https://";
    const HTTP_PROTOCOL = "http://";
    const CUSTOM_DOMAIN_TYPES = ["site", "group", "persona"];

    public function __construct(
        App &$app,
        Companies $companies,
        CompanySettings $companySettings,
        CardDomains $cardDomains,
        Cards $cards,
        array &$server)
    {
        $this->app = $app;
        $this->appSession = $this->app->getAppSession();
        $this->companies = $companies;
        $this->companySettings = $companySettings;
        $this->cardDomains = $cardDomains;
        $this->cards = $cards;
        $this->server = $server;
    }

    public function assignCustomPlatform(): bool
    {
        if ($this->isNotAnAppDomain()) {
            return false;
        }

        if (!$this->appCustomPlatformDataInSession() && !$this->loadCustomPlatformFromDatabase()) {
            // We have no what platform this is. Generic 404 page.
            $this->whiteLabelFound = false;
            return false;
        }

        return true;
    }

    public function assignDomainName(): bool
    {
        if ($this->isNotACustomDomain()) {
            return false;
        }

        if (!$this->appCustomDomainDataInSession() && !$this->loadCustomDomainFromDatabase()) {
            return false;
        }

        return true;
    }

    public function getCustomPlatform(): AppCustomPlatform
    {
        return $this->customPlatform;
    }

    public function getActiveDomain(): AppCustomDomain
    {
        return !empty($this->server["HTTP_HOST"]) && $this->publicDomain->getDomain() === $this->server["HTTP_HOST"] ? $this->getPublicDomain() : $this->getPortalDomain();
    }

    public function getPublicDomain(): AppCustomDomain
    {
        return $this->publicDomain;
    }

    public function getPortalDomain(): AppCustomDomain
    {
        return $this->portalDomain;
    }

    public function getAppDomain(string $appName): AppCustomDomain
    {
        return str_replace("__app__", $appName, $this->appDomain);
    }

    protected function isNotAnAppDomain(): bool
    {
        return !empty($this->app->objAppSession["Core"]["App"]["DomainType"]) && $this->app->objAppSession["Core"]["App"]["DomainType"] !== "app";
    }

    protected function isNotACustomDomain(): bool
    {
        return !empty($this->app->objAppSession["Core"]["App"]["DomainType"]) && !in_array($this->app->objAppSession["Core"]["App"]["DomainType"], self::CUSTOM_DOMAIN_TYPES);
    }

    protected function appCustomPlatformDataInSession(): bool
    {
        $whiteLabel = $this->appSession["Core"]["App"]["WhiteLabel"] ?? null;

        if ($whiteLabel === null) {
            return false;
        }

        $whiteLabelSettings = $this->appSession["Core"]["App"]["WhiteLabelSettings"] ?? [];

        $this->assignWhiteLabelPortal($whiteLabel);
        $this->assignWhiteLabelPublic($whiteLabel);
        $this->assignAppDomain($whiteLabel);

        $web_type = $this->appSession["Core"]["App"]["DomainType"] ?? "";

        $this->registerWhiteLabel($whiteLabel, $whiteLabelSettings, $web_type);

        return true;
    }

    protected function loadCustomPlatformFromDatabase(): bool
    {
        $whiteLabel = $this->getActiveWhiteLabel();

        if ($whiteLabel === null) {
            return false;
        }

        $whiteLabelArray = $whiteLabel->ToArray();
        $whiteLabelSettings = $this->getActiveWhiteLabelSettings($whiteLabel->company_id);
        $whiteLabelSettingsArray = $whiteLabelSettings->ToPublicArray();

        $this->assignWhiteLabelPortal($whiteLabelArray);
        $this->assignWhiteLabelPublic($whiteLabelArray);
        $this->assignAppDomain($whiteLabelArray);

        $this->appSession["Core"]["App"]["WhiteLabel"] = $whiteLabelArray;
        $this->appSession["Core"]["App"]["WhiteLabelSettings"] = $whiteLabelSettingsArray;

        $this->appSession["Core"]["App"]["DomainType"] = "app";
        $this->app->setAppSession($this->appSession);

        $this->registerWhiteLabel($whiteLabelArray, $whiteLabelSettings->ToPublicArray(), "app");

        return true;
    }

    protected function assignWhiteLabelPortal(array $whiteLabelArray) : void
    {
        $portal_domain = $whiteLabelArray["domain_portal"] ?? "";;
        $portal_fullDomain = ($whiteLabelArray["domain_portal_ssl"] === true ? self::HTTPS_PROTOCOL : self::HTTP_PROTOCOL) . $portal_domain;
        $portal_ssl = $whiteLabelArray["domain_portal_ssl"] == true;
        $this->setPortalDomain($portal_domain, $portal_fullDomain, $portal_ssl, $whiteLabelArray["domain_portal_name"] ?? "");

        $this->appSession["Core"]["App"]["Domain"]["Portal"] = $portal_domain;
        $this->appSession["Core"]["App"]["Domain"]["Portal_SSL"] = $portal_ssl;
        $this->app->setAppSession($this->appSession);
    }

    protected function assignWhiteLabelPublic(array $whiteLabelArray) : void
    {
        $web_domain = $whiteLabelArray["domain_public"] ?? "";
        $web_fullDomain = ($whiteLabelArray["domain_public_ssl"] === true ? self::HTTPS_PROTOCOL : self::HTTP_PROTOCOL) . $web_domain;
        $web_ssl = $whiteLabelArray["domain_public_ssl"] == true;
        $this->setPublicDomain($web_domain, $web_fullDomain, $web_ssl, "app", $whiteLabelArray["domain_public_name"] ?? "");

        $this->appSession["Core"]["App"]["Domain"]["Web"] = $web_domain;
        $this->appSession["Core"]["App"]["Domain"]["Web_SSL"] = $web_ssl;
        $this->app->setAppSession($this->appSession);
    }

    protected function assignSiteDomainPublic(array $cardDomain) : void
    {
        $site_domain = $cardDomain["domain_name"] ?? "";
        $site_fullDomain = ($cardDomain["ssl"] ? self::HTTPS_PROTOCOL : self::HTTP_PROTOCOL) . $site_domain;
        $web_ssl = $cardDomain["ssl"] == true;
        $web_type = $cardDomain["type"] ?? "";
        $this->setPublicDomain($site_domain, $site_fullDomain, $web_ssl, $web_type, $cardDomain["card"]["card_name"] ?? "");

        $this->appSession["Core"]["App"]["Domain"]["Web"] = $site_domain;
        $this->appSession["Core"]["App"]["Domain"]["Web_SSL"] = $web_ssl;
        $this->publicDomain->setCardId($cardDomain["card_id"]);
    }

    protected function assignAppDomain(array $whiteLabelArray) : void
    {
        $app_domain = "__app__.". $whiteLabelArray["domain_public"] ?? "";
        $app_ssl = ($whiteLabelArray["domain_public_ssl"] === true);
        $app_fullDomain = ($app_ssl ? self::HTTPS_PROTOCOL : self::HTTP_PROTOCOL) . $app_domain;
        $this->setAppDomain($app_domain, $app_fullDomain, $app_ssl, $whiteLabelArray["domain_public_name"] ?? "");

        $this->appSession["Core"]["App"]["Domain"]["App"] = $app_domain;
        $this->appSession["Core"]["App"]["Domain"]["App_SSL"] = $app_ssl;
        $this->app->setAppSession($this->appSession);
    }

    protected function appCustomDomainDataInSession() : bool
    {
        $cardDomain = $this->appSession["Core"]["App"]["CardDomain"] ?? null;
        $whiteLabel = $this->appSession["Core"]["App"]["WhiteLabel"] ?? null;

        if ($cardDomain === null || $whiteLabel === null) {
            return false;
        }

        $whiteLabelSettings = $this->appSession["Core"]["App"]["WhiteLabelSettings"] ?? [];

        $this->assignSiteDomainPublic($cardDomain);
        $this->assignWhiteLabelPortal($whiteLabel);
        $this->assignAppDomain($whiteLabel);

        $web_type = $cardDomain["type"] ?? "";
        $this->registerWhiteLabel($whiteLabel, $whiteLabelSettings, $web_type);

        return true;
    }

    protected function loadCustomDomainFromDatabase() : bool
    {
        $domain = $this->getActiveCustomDomain();

        if ($domain === null) {
            return false;
        }

        $whiteLabel = $this->getWhiteLabelByCard($domain);
        $this->assignCustomPlatformFromEntity($whiteLabel, $domain);

        return true;
    }

    public function loadCustomDefaultDomainFromDatabase() : bool
    {
        $domain = $this->getDefaultCustomDomain();

        if ($domain === null) {

            return false;
        }

        $whiteLabel = $this->getWhiteLabelByCard($domain);
        $this->assignCustomPlatformFromEntity($whiteLabel, $domain);

        return true;
    }

    protected function assignCustomPlatformFromEntity(?CompanyModel $whiteLabel, ?CardDomainModel $domain): void
    {
        $whiteLabelArray = $whiteLabel->ToArray();

        $whiteLabelSettings = $this->getActiveWhiteLabelSettings($whiteLabel->company_id);
        $whiteLabelSettingsArray = $whiteLabelSettings->ToPublicArray();

        $web_domain = $domain->domain_name ?? "";
        $web_fullDomain = ($domain->ssl === true ? self::HTTPS_PROTOCOL : self::HTTP_PROTOCOL) . $web_domain;
        $web_ssl = ($domain->ssl === true);
        $web_type = $domain->type ?? "";
        $this->setPublicDomain($web_domain, $web_fullDomain, $web_ssl, $web_type, $domain->card->card_name ?? "");
        $this->publicDomain->setCardId($domain->card_id);

        $portal_domain = $whiteLabelArray["domain_portal"] ?? "";
        $portal_fullDomain = ($whiteLabelArray["domain_portal_ssl"] === true ? self::HTTPS_PROTOCOL : self::HTTP_PROTOCOL) . $web_domain;
        $portal_ssl = ($whiteLabelArray["domain_portal_ssl"] === true);
        $this->setPortalDomain($portal_domain, $portal_fullDomain, $portal_ssl, $whiteLabelArray["domain_portal_name"] ?? "");

        $app_domain = "__app__.". $whiteLabelArray["domain_public"] ?? "";
        $app_ssl = ($whiteLabelArray["domain_public_ssl"] === true);
        $app_fullDomain = ($app_ssl ? self::HTTPS_PROTOCOL : self::HTTP_PROTOCOL) . $app_domain;
        $this->setAppDomain($app_domain, $app_fullDomain, $app_ssl, $whiteLabelArray["domain_public_name"] ?? "");

        $this->appSession["Core"]["App"]["WhiteLabel"] = $whiteLabelArray;
        $this->appSession["Core"]["App"]["WhiteLabelSettings"] = $whiteLabelSettingsArray;
        $this->appSession["Core"]["App"]["Domain"]["Web"] = $web_domain;
        $this->appSession["Core"]["App"]["Domain"]["Web_SSL"] = $web_ssl;
        $this->appSession["Core"]["App"]["Domain"]["App"] = $app_domain;
        $this->appSession["Core"]["App"]["Domain"]["App_SSL"] = $app_ssl;
        $this->appSession["Core"]["App"]["Domain"]["Portal"] = $portal_domain;
        $this->appSession["Core"]["App"]["Domain"]["Portal_SSL"] = $portal_ssl;
        $this->appSession["Core"]["App"]["DomainType"] = $domain->type;
        $this->appSession["Core"]["App"]["CardDomain"] = $domain->ToArray();

        $this->app->setAppSession($this->appSession);

        $this->registerWhiteLabel($whiteLabelArray, $whiteLabelSettings->ToPublicArray(), $web_type);
    }

    protected function getActiveCustomDomain(): ?CardDomainModel
    {
        $cardDomain = $this->cardDomains->getWhere(["domain_name" => $this->server["HTTP_HOST"] ?? ""])->getData()->first();

        if ($cardDomain === null) {
            return null;
        }

        $card = $this->cards->getById($cardDomain->card_id)->getData()->first();

        if ($card === null) {
            return null;
        }

        $cardDomain->AddUnvalidatedValue("card", $card);

        return $cardDomain;
    }

    protected function getDefaultCustomDomain(): ?CardDomainModel
    {
        $cardDomain = $this->cardDomains->getWhere(["company_id" => 0])->getData()->first();

        if ($cardDomain === null) {
            return null;
        }

        $card = $this->cards->getById($cardDomain->card_id)->getData()->first();

        if ($card === null) {
            return null;
        }

        $cardDomain->AddUnvalidatedValue("card", $card);

        return $cardDomain;
    }

    protected function getWhiteLabelByCard(CardDomainModel $cardDomain): ?CompanyModel
    {
        if (
            str_contains($this->server["HTTP_HOST"], "localhost")
            || $this->server["HTTP_HOST"] === "excell.docker")
        {
            return $this->mockUpLocalCompany();
        }

        if (empty($cardDomain->card)) {
            return null;
        }

        $companyResult = $this->companies->getById($cardDomain->card->company_id);

        return $companyResult->getData()->first();
    }

    protected function getActiveWhiteLabel(): ?CompanyModel
    {
        $httpHost = ($this->server["HTTP_HOST"] ?? "");
        if (
            str_contains($httpHost, "localhost")
            || $httpHost === "excell.docker")
        {
            return $this->mockUpLocalCompany();
        }

        $companyResult = $this->companies->getWhere([["domain_public" => $httpHost], "OR", ["domain_portal" => $httpHost]]);

        return $companyResult->getData()->first();
    }

    protected function getActiveWhiteLabelSettings(int $companyId): ExcellCollection
    {
        $companySettingResult = $this->companySettings->getByCompanyId($companyId);
        return $companySettingResult->getData();
    }

    protected function registerWhiteLabel(array $company, array $companySettings, string $domainType): void
    {
        $companyId = $company["company_id"] ?? $this->app->getEnv("DEFAULT_COMPANY_ID") ?? 0;
        $parentId = $company["parent_id"] ?? $company["company_id"] ?? $this->app->getEnv("DEFAULT_COMPANY_ID") ?? 0;
        $this->customPlatform = new AppCustomPlatform(
            $this->companies,
            $this->companySettings,
            $this->publicDomain,
            $this->portalDomain,
            $this->appDomain,
            $companyId,
            $parentId
        );

        $this->customPlatform->hydrateCompanyFromCache($company, $companySettings);
        $this->customPlatform->loadCompanySettings($companyId);

        $this->app->objAppSession["Core"]["App"]["DomainType"] = $domainType;
    }

    protected function setPublicDomain(string $domain, string $domainFull, bool $ssl, string $type, string $name): void
    {
        $this->publicDomain = new AppCustomDomain($domain, $domainFull, $ssl, $name, $type);
    }

    protected function setPortalDomain(string $domain, string $domainFull, bool $ssl, string $name): void
    {
        $this->portalDomain = new AppCustomDomain($domain, $domainFull, $ssl, $name);
    }

    protected function setAppDomain(string $domain, string $domainFull, bool $ssl, string $name): void
    {
        $this->appDomain = new AppCustomDomain($domain, $domainFull, $ssl, $name);
    }

    protected function mockUpLocalCompany(): CompanyModel
    {
        return new CompanyModel([
            "company_id" => 0,
            "company_name" => "Excell Local Developer",
            "platform_name" => "Excell Local",
            "parent_id" => EXCELL_NULL,
            "status" => "active",
            "domain_portal" => $this->server["HTTP_HOST"],
            "domain_portal_ssl" => false,
            "domain_portal_name" => "Excell Admin",
            "domain_public" => $this->server["HTTP_HOST"],
            "domain_public_ssl" => false,
            "domain_public_name" => "Excell App"
        ]);
    }

    public function checkForLocalhost(): bool
    {
        return str_contains($this->getActiveDomain()->getDomain(), "localhost");
    }

    public function isInactivePlatform() : bool
    {
        switch($this->getCustomPlatform()->getCompany()->status) {
            case "inactive":
            case "canceled":
            case "suspended":
                return true;
            default:
                return false;
        }
    }

    public function isComingSoonPlatform() : bool
    {
        return $this->getCustomPlatform()->getCompany()->status === "pending";
    }

    public function isWhiteLabelFound(): bool
    {
        return $this->whiteLabelFound;
    }
}