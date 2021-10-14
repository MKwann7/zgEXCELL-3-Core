<?php

namespace Entities\Mobiniti\Models;

use DateTime;

/** @property string $id
 *  @property datetime $created_at
 *  @property datetime $updated_at
 *  @property string $message
 *  @property bool $read
 *  @property string $contact_id
 *  @property string $contact
 */
class MobinitiMessageModel extends MobinitiModel
{
    protected $EntityName = "Mobiniti";
    protected $ModelName = "MobinitiMessage";

    public function __construct($entityData = null)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData);
    }

    private function loadDefinitions()
    {
        return [
            "id" =>["type" => "varchar", "length" => "36"],
            "created_at" =>["type" => "datetime"],
            "updated_at" =>["type" => "datetime"],
            "message" =>["type" => "varchar", "length" => "500"],
            "read" =>["type" => "bool"],
            "contact_id" =>["type" => "varchar", "length" => "36"],
            "contact" =>["type" => "entity:" . MobinitiContactModel::class, "nullable" => true]
        ];
    }
}