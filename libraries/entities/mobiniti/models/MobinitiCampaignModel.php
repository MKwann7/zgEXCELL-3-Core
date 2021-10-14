<?php

namespace Entities\Mobiniti\Models;

use DateTime;

/** @property string $id
 *  @property datetime $created_at
 *  @property datetime $updated_at
 *  @property string $name
 *  @property string $to
 *  @property string $message
 *  @property bool $message_pending_contacts
 *  @property datetime $send_date
 */
class MobinitiCampaignModel extends MobinitiModel
{
    protected $EntityName = "Mobiniti";
    protected $ModelName = "MobinitiCampaign";

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
            "name" =>["type" => "varchar", "length" => "50"],
            "to" =>["type" => "varchar", "length" => "36"],
            "message" =>["type" => "varchar", "length" => "500"],
            "message_pending_contacts" =>["type" => "bool"],
            "send_date" =>["type" => "datetime"],
        ];
    }
}