<?php

namespace Entities\Mobiniti\Models;

use DateTime;

/** @property string $id
 *  @property string $keyword
 *  @property string $name
 *  @property string $join_message
 *  @property bool $one_time_message
 *  @property bool $always_send_join
 *  @property bool $always_send_optin
 *  @property bool $social_profiling
 *  @property bool $email_new_contact
 *  @property array $emails
 *  @property array $status
 *  @property datetime $created_at
 *  @property datetime $updated_at
 *  @property bool $optin
 */
class MobinitiGroupModel extends MobinitiModel
{
    protected $EntityName = "Mobiniti";
    protected $ModelName = "MobinitiGroup";

    public function __construct($entityData = null)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData);
    }

    private function loadDefinitions()
    {
        return [
            "id" =>["type" => "varchar", "length" => "36"],
            "order_line_id" =>["type" => "int", "length" => "50", "nullable" => true],
            "card_id" =>["type" => "int", "length" => "50","nullable" => true],
            "keyword" =>["type" => "varchar", "length" => 50],
            "type" =>["type" => "varchar", "length" => "15","nullable" => true],
            "name" =>["type" => "varchar", "length" => "50"],
            "join_message" =>["type" => "varchar", "length" => "250"],
            "one_time_message" =>["type" => "bool","nullable" => true],
            "always_send_join" =>["type" => "bool","nullable" => true],
            "always_send_optin" =>["type" => "bool","nullable" => true],
            "social_profiling" =>["type" => "bool","nullable" => true],
            "email_new_contact" =>["type" => "bool","nullable" => true],
            "emails" =>["type" => "array"],
            "created_at" =>["type" => "datetime"],
            "updated_at" =>["type" => "datetime"],
            "optin" =>["type" => "bool"],
            "status" =>["type" => "array"],
        ];
    }
}