<?php

namespace Entities\Cards\Models;

use App\Core\AppModel;

class CardTemplateModel extends AppModel
{
    protected string $EntityName = "Cards";
    protected string $ModelName = "CardTemplate";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions(): array
    {
        return [
            "card_template_id" => ["type" => "int","length" => 15],
            "company_id" => ["type" => "int","length" => 15,"fk" => ["table" => "company","key" => "company_id","value" => "company_name"]],
            "division_id" => ["type" => "int","length" => 15,"fk" => ["table" => "division","key" => "division_id","value" => "division_name"]],
            "name" => ["type" => "varchar","length" => 0],
            "template_type" => ["type" => "varchar","length" => 25],
            "data" => ["type" => "json"],
            "sys_row_id" => ["type" => "char","length" => 36]
        ];
    }
}