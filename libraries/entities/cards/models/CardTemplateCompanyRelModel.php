<?php

namespace Entities\Cards\Models;

use App\Core\AppModel;

class CardTemplateCompanyRelModel extends AppModel
{
    protected string $EntityName = "Cards";
    protected string $ModelName = "CardTemplateCompanyRel";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions(): array
    {
        return [
            "card_template_company_rel_id" => ["type" => "int","length" => 15],
            "card_template_id" => ["type" => "int","length" => 15],
            "company_id" => ["type" => "int","length" => 15,"fk" => ["table" => "company","key" => "company_id","value" => "company_name"]],
            "sys_row_id" => ["type" => "char","length" => 36]
        ];
    }
}