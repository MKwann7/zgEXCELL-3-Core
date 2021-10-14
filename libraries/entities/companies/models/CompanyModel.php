<?php

namespace Entities\Companies\Models;

use App\Core\AppModel;

class CompanyModel extends AppModel
{
    protected $EntityName = "Company";
    protected $ModelName = "Company";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "company_id" => ["type" => "int","length" => 15],
            "company_name" => ["type" => "varchar","length" => 50],
            "platform_name" => ["type" => "varchar","length" => 50],
            "owner_id" => ["type" => "int","length" => 15],
            "default_sponsor_id" => ["type" => "int","length" => 15],
            "status" => ["type" => "varchar","length" => 15],
            "parent_id" => ["type" => "int","length" => 15],
            "domain_portal" => ["type" => "varchar","length" => 75],
            "domain_portal_ssl" => ["type" => "boolean"],
            "domain_portal_name" => ["type" => "varchar","length" => 35],
            "domain_public" => ["type" => "varchar","length" => 75],
            "domain_public_ssl" => ["type" => "boolean"],
            "domain_public_name" => ["type" => "varchar","length" => 35],
            "public_domain_404_redirect" => ["type" => "varchar","length" => 75],
            "address_1" => ["type" => "varchar","length" => 50],
            "address_2" => ["type" => "varchar","length" => 35],
            "address_3" => ["type" => "varchar","length" => 25],
            "city" => ["type" => "varchar","length" => 45],
            "state" => ["type" => "varchar","length" => 35],
            "country" => ["type" => "varchar","length" => 35],
            "phone_number" => ["type" => "varchar","length" => 20],
            "fein" => ["type" => "int","length" => 9],
            "legal_name" => ["type" => "varchar","length" => 200],
            "customer_support_email" => ["type" => "varchar","length" => 150],
            "logo_url" => ["type" => "varchar","length" => 150],
            "created_on" => ["type" => "datetime"],
            "last_updated" => ["type" => "datetime"],
            "sys_row_id" => ["type" => "char","length" => 36]
        ];
    }
}