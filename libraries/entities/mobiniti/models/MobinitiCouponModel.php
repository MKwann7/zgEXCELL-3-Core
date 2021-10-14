<?php

namespace Entities\Mobiniti\Models;

use DateTime;

/** @property string $id
 *  @property datetime $created_at
 *  @property datetime $updated_at
 *  @property string $name
 *  @property string $offer
 *  @property string $gender
 * @property datetime $start
 * @property datetime $end
 *  @property bool $max_repemptions
 *  @property int $upc
 *  @property string $terms
 *  @property bool $mobile_wallets
 *  @property string $accent_color
 *  @property string $company_name
 *  @property string $phone_number
 *  @property string $address1
 *  @property string $address2
 *  @property array $template
 */
class MobinitiCouponModel extends MobinitiModel
{
    protected $EntityName = "Mobiniti";
    protected $ModelName = "MobinitiCoupon";

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
            "offer" =>["type" => "varchar", "length" => "50"],
            "gender" =>["type" => "varchar", "length" => "25"],
            "start" =>["type" => "datetime"],
            "end" =>["type" => "datetime"],
            "max_redemptions" =>["type" => "bool"],
            "upc" =>["type" => "integer", "length" => "15"],
            "terms" =>["type" => "varchar", "length" => "250"],
            "mobile_wallets" =>["type" => "bool"],
            "accent_color" =>["type" => "varchar", "length" => "10"],
            "company_name" =>["type" => "varchar", "length" => "100"],
            "phone_number" =>["type" => "varchar", "length" => "20"],
            "address1" =>["type" => "varchar", "length" => "75"],
            "address2" =>["type" => "varchar", "length" => "25"],
            "template" =>["type" => "array"],
        ];
    }
}