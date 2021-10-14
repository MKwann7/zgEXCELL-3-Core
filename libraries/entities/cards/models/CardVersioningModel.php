<?php

namespace Entities\Cards\Models;

use App\Core\AppModel;

class CardVersioningModel extends AppModel
{
    protected $EntityName = "Cards";
    protected $ModelName = "Card";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "card_version_id" => [ "type" => "int", "length" => 15],
            "card_version_status" => [ "type" => "varchar", "length" => 25],
            "card_id" => [ "type" => "int", "length" => 15],
            "owner_id" => [ "type" => "int", "length" => 15, "fk" => [  "table" => "user",  "key" => "user_id",  "value" => "username" ]],
            "card_user_id" => [ "type" => "int", "length" => 15, "fk" => [  "table" => "user",  "key" => "user_id",  "value" => "username" ]],
            "division_id" => [ "type" => "int", "length" => 15, "fk" => [  "table" => "division",  "key" => "division_id",  "value" => "division_name" ]],
            "company_id" => [ "type" => "int", "length" => 15, "fk" => [  "table" => "company",  "key" => "company_id",  "value" => "company_name" ]],
            "card_type_id" => [ "type" => "int", "length" => 5, "fk" => [  "table" => "card_type",  "key" => "card_type_id",  "value" => "name" ]],
            "card_name" => [ "type" => "varchar", "length" => 255],
            "status" => [ "type" => "varchar", "length" => 15],
            "template_card" => [ "type" => "boolean"],
            "order_line_id" => [ "type" => "int", "length" => 15],
            "product_id" => [ "type" => "int", "length" => 15, "fk" => [  "table" => "product",  "key" => "product_id",  "value" => "title" ]],
            "template_id" => [ "type" => "int", "length" => 15, "fk" => [  "table" => "card_template",  "key" => "card_template_id",  "value" => "name" ]],
            "card_vanity_url" => [ "type" => "varchar", "length" => 25],
            "card_keyword" => [ "type" => "varchar", "length" => 50],
            "card_num" => [ "type" => "int", "length" => 15],
            "redirect_to" => [ "type" => "int", "length" => 5],
            "card_data" => [ "type" => "json", "length" => 0],
            "created_on" => [ "type" => "datetime", "length" => 0],
        ];
    }
}