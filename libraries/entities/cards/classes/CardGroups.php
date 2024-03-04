<?php

namespace Entities\Cards\Classes;

use App\Core\AppEntity;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Cards\Models\CardGroupModel;

class CardGroups extends AppEntity
{
    public string $strEntityName       = "Cards";
    public $strDatabaseTable    = "card_rel_group";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = CardGroupModel::class;
    public $strMainModelPrimary = "card_rel_group_id";

    public function GetAllCardGroupsForDisplay() : ExcellTransaction
    {
        $objCardGroups = $this->getWhere(["status", "!=", "Disabled"]);

        return $objCardGroups;
    }
}