<?php

namespace Entities\Cards\Classes;

use App\Core\AppEntity;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Cards\Models\CardSettingModel;

class CardSettings extends AppEntity
{
    public string $strEntityName       = "Cards";
    public $strDatabaseTable    = "card_setting";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = CardSettingModel::class;
    public $strMainModelPrimary = "card_setting_id";

    public function getByCardId($cardId) : ExcellTransaction
    {
        return $this->getWhere(["card_id" => $cardId]);
    }
}