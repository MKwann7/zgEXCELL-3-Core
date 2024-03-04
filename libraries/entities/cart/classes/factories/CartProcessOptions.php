<?php

namespace Entities\Cart\Classes\Factories;

use App\Utilities\Excell\ExcellCollection;
use App\Utilities\Excell\ExcellModel;

/**
 * @property string $parent_entity_type
 * @property int $parent_entity_id
 * @property int $company_id
 * @property int $division_id
 * @property int $default_user_id
 * @property string $creation_date_override
 * @property int $page_create_count_override
 * @property float $purchase_price_override
 * @property bool $skip_emails
 * @property ExcellCollection $widgets_for_purchase
 */

class CartProcessOptions extends ExcellModel
{
    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
    }

    private function loadDefinitions(): array
    {
        return [
            "parent_entity_type" => ["type" => "varchar","length" => 25],
            "parent_entity_id" => ["type" => "int","length" => 15],
            "company_id" => ["type" => "int","length" => 15],
            "division_id" => ["type" => "int","length" => 15],
            "default_user_id" => ["type" => "int","length" => 15],
            "creation_date_override" => ["type" => "string"],
            "page_create_count_override" => ["type" => "int", "length" => 2],
            "purchase_price_override" => ["type" => "decimal"],
            "skip_emails" => ["type" => "boolean"],
            "widgets_for_purchase" => ["type" => ExcellCollection::class],
        ];
    }
}