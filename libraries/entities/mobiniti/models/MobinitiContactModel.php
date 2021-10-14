<?php

namespace Entities\Mobiniti\Models;

use DateTime;

/** @property string $id
 *  @property datetime $created_at
 *  @property datetime $updated_at
 *  @property string $first_name
 *  @property string $last_name
 *  @property string $gender
 * @property datetime $birth_date
 *  @property string $phone_number
 *  @property bool $email
 *  @property bool $reward_points
 *  @property bool $country_code
 *  @property array $status
 *  @property MobinitiGroupModel[] $groups
 *  @property array $custom_fields
 */
class MobinitiContactModel extends MobinitiModel
{
    protected $EntityName = "Mobiniti";
    protected $ModelName = "MobinitiContact";

    public function __construct($entityData = null)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData);
    }

    private function loadDefinitions()
    {
        return [
            "id" =>["type" => "varchar", "length" => "36"],
            "contact_id" =>["type" => "int", "length" => "15", "nullable" => true],
            "created_at" =>["type" => "datetime"],
            "updated_at" =>["type" => "datetime"],
            "first_name" =>["type" => "varchar", "length" => "50", "nullable" => true],
            "last_name" =>["type" => "varchar", "length" => "50", "nullable" => true],
            "gender" =>["type" => "varchar", "length" => "25", "nullable" => true],
            "birth_date" =>["type" => "datetime", "nullable" => true],
            "phone_number" =>["type" => "varchar", "length" => "20", "nullable" => true],
            "email" =>["type" => "varchar", "length" => "100", "nullable" => true],
            "reward_points" =>["type" => "int", "nullable" => true],
            "country_code" =>["type" => "int", "nullable" => true],
            "status" =>["type" => "array", "nullable" => true],
            "groups" =>["type" => "collection:" . MobinitiGroupModel::class, "nullable" => true],
            "custom_fields" =>["type" => "array", "nullable" => true],
        ];
    }
}