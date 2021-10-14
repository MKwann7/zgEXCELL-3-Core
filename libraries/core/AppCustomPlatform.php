<?php

namespace App\Core;

use App\Utilities\Excell\ExcellCollection;
use Entities\Companies\Classes\Companies;
use Entities\Companies\Classes\CompanySettings;
use Entities\Companies\Models\CompanyModel;

class AppCustomPlatform
{
    protected $company;
    protected $company_settings;
    protected $company_id;
    protected $parent_id;
    protected $active_domain;
    protected $same_domain = true;
    protected $public_domain;
    protected $public_domain_full;
    protected $public_domain_ssl;
    protected $public_domain_name;
    protected $portal_domain;
    protected $portal_domain_full;
    protected $portal_domain_ssl;
    protected $portal_domain_name;

    protected $websiteName;
    protected $websiteDomain;
    protected $websiteDomainFull;

    public function __construct ($companyId = 0, $parentId = 0, $activeDomain = "", bool $sameDomain = true, $whiteLabel = null, $website = null)
    {
        $this->company_id = $companyId;
        $this->parent_id = $parentId;
        $this->active_domain = $activeDomain;
        $this->same_domain = $sameDomain;
        $this->public_domain = $whiteLabel["Web"] ?? env("WEBSITE_URL");
        $this->public_domain_full = $whiteLabel["WebFull"] ?? "http://" . env("WEBSITE_URL");
        $this->public_domain_name = $whiteLabel["WebTitle"] ?? env("WEBSITE_TITLE");
        $this->public_domain_ssl = $whiteLabel["WebSSL"] ?? false;
        $this->portal_domain = $whiteLabel["Portal"] ?? env("WEBSITE_URL");
        $this->portal_domain_full = $whiteLabel["PortalFull"] ?? "http://" . env("WEBSITE_URL");
        $this->portal_domain_name = $whiteLabel["PortalTitle"] ?? env("WEBSITE_TITLE");
        $this->portal_domain_ssl = $whiteLabel["PortalSSL"] ?? false;

        $this->websiteName = $website["MetaTitleName"] ?? env("WEBSITE_TITLE");;
        $this->websiteDomain = $website["DomainName"] ?? env("WEBSITE_URL");;
        $this->websiteDomainFull = $website["FullUrl"] ?? "http://" . env("WEBSITE_URL");;
    }

    public function addCompany(CompanyModel $company) : self
    {
        $this->company = $company;
        $objCompanySettings = new CompanySettings();
        $companySettingResult = $objCompanySettings->getByCompanyId($company->company_id);

        $this->company_settings = $companySettingResult->Data;

        return $this;
    }

    public function getCompany() : ?CompanyModel
    {
        if (!isset($this->company_id))
        {
            return null;
        }

        if (empty($this->company))
        {
            $companies = new Companies();
            $companyResult = $companies->getById($this->company_id);

            if ($companyResult->Result->Count !== 1)
            {
                return null;
            }

            $this->company = $companyResult->Data->First();
        }


        return $this->company;
    }

    public function refreshCompany() : self
    {
        $companies = new Companies();
        $companyResult = $companies->getById($this->company_id);

        if ($companyResult->Result->Count === 0)
        {
            $this->blnNoDomain = true;
            return $this;
        }

        return $this->addCompany($companyResult->Data->First());
    }

    public function getCompanySettings() : ExcellCollection
    {
        return $this->company_settings ?? new ExcellCollection();
    }

    public function getCompanyId() : int
    {
        return floatval($this->company_id ?? 0);
    }

    public function getCompanyParentId() : int
    {
        return floatval($this->parent_id ?? $this->company_id ?? 0);
    }

    public function getPublicDomain() : string
    {
        return $this->public_domain;
    }

    public function getFullPublicDomain() : string
    {
        return $this->public_domain_full;
    }

    public function getPortalDomain() : string
    {
        return $this->portal_domain;
    }

    public function getFullPortalDomain() : string
    {
        return $this->portal_domain_full;
    }

    public function isSameDomain() : bool
    {
        return $this->same_domain;
    }

    public function getActiveDomain() : string
    {
        return $this->websiteDomain;
    }

    public function getActiveWebTitle() : string
    {
        return $this->websiteName;
    }

    public function getActiveDomainFull() : string
    {
        return $this->websiteDomainFull;
    }

    public function getPortalName() : string
    {
        return $this->portal_domain_name;
    }

    public function getPublicName() : string
    {
        return $this->public_domain_name;
    }
}