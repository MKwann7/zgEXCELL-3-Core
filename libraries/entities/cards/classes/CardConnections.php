<?php

namespace Entities\Cards\Classes;

use App\Core\AppEntity;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Cards\Classes\Cards;
use Entities\Cards\Models\CardConnectionModel;

class CardConnections extends AppEntity
{
    public string $strEntityName       = "Cards";
    public $strDatabaseTable    = "connection_rel";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = CardConnectionModel::class;
    public $strMainModelPrimary = "connection_rel_id";

    public function GetByCardId($intCardId) : ExcellTransaction
    {
        $objTransaction = new ExcellTransaction();

        if (empty($intCardId))
        {
            $objTransaction->result->Success = false;
            $objTransaction->result->Count = 0;
            $objTransaction->result->Message = "You must pass in an id to retrieve a " . $this->strMainModelName . " row.";

            return $objTransaction;
        }

        $objCardModule = new Cards();
        $objCardResult = $objCardModule->getById($intCardId);

        if($objCardResult->result->Count === 0)
        {
            $objTransaction->result->Success = false;
            $objTransaction->result->Count = 0;
            $objTransaction->result->Message = "No card was found with id " . $intCardId . ".";

            return $objTransaction;
        }

        $objCard = $objCardResult->getData()->first();
        $objCard->LoadCardTemplate();
        $intCardConnectionsCount = $objCard->Template->getData()->connections->count;

        $lstConnectionResult = new ExcellTransaction();

        for($currConnectionCount = 1; $currConnectionCount <= $intCardConnectionsCount; $currConnectionCount++)
        {
            $objConnectionResult = $this->getWhere(["card_id" => $intCardId, "display_order" => $currConnectionCount],"connection_rel_id.DESC",1);
            $lstConnectionResult->getData()->Add($objConnectionResult->getData()->first()->connection_rel_id, $objConnectionResult->getData()->first());
        }

        $lstConnectionResult->result->Success = true;
        $lstConnectionResult->result->Count = $lstConnectionResult->getData()->Count();

        return  $lstConnectionResult;
    }
}