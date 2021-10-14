<?php

namespace Entities\Mobiniti\Models;

/** @property int $id
 *  @property string $mobiniti_contact_id
 *  @property string $user_id
 */
class MobinitiContactUserRelModel extends MobinitiModel
{
    protected $EntityName = "Mobiniti";
    protected $ModelName = "MobinitiContactUserRel";

    public function __construct($entityData = null)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData);
    }

    private function loadDefinitions()
    {
        return [
            "mobiniti_contact_user_rel_id" =>["type" => "int", "length" => "15"],
            "mobiniti_contact_id" =>["type" => "varchar", "length" => "36"],
            "user_id" =>["type" => "int", "length" => "15"],
        ];
    }
}