<?php

namespace Entities\Mobiniti\Models;

/** @property string $id
 *  @property string $name
 *  @property string $description
 *  @property bool $enabled
 */
class MobinitiOptInModel extends MobinitiModel
{
    protected string $EntityName = "Mobiniti";
    protected string $ModelName = "MobinitiOptIn";

    public function __construct($entityData = null)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData);
    }

    private function loadDefinitions(): array
    {
        return [
            "id" =>["type" => "varchar", "length" => "36"],
            "name" =>["type" => "varchar", "length" => "50"],
            "description" =>["type" => "varchar", "length" => "250"],
            "enabled" =>["type" => "bool"],
        ];
    }
}