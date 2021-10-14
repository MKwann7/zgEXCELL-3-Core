<?php

namespace Entities\Mobiniti\Models;

/** @property string $id
 *  @property \datetime $created_at
 *  @property \datetime $updated_at
 *  @property string $name
 */
class MobinitiCarrierModel extends MobinitiModel
{
    protected $EntityName = "Mobiniti";
    protected $ModelName = "MobinitiCarrier";

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
        ];
    }
}