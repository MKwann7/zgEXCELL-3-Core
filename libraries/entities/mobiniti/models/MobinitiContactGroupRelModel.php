<?php

namespace Entities\Mobiniti\Models;

/** @property int $mobiniti_contact_group_rel_id
 *  @property int $card_id
 *  @property string $mobiniti_contact_id
 *  @property string $mobiniti_group_id
 *  @property string $created_on
 */
class MobinitiContactGroupRelModel extends MobinitiModel
{
    protected $EntityName = "Mobiniti";
    protected $ModelName = "MobinitiContactGroupRel";

    public function __construct($entityData = null)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData);
    }

    private function loadDefinitions()
    {
        return [
            "mobiniti_contact_group_rel_id" =>["type" => "int", "length" => "15"],
            "card_id" =>["type" => "int", "length" => "15"],
            "mobiniti_contact_id" =>["type" => "varchar", "length" => "36"],
            "mobiniti_group_id" =>["type" => "varchar", "length" => "36"],
            "created_on" => ["type" => "datetime", "nullable" => true]
        ];
    }
}