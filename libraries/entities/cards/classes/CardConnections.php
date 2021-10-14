<?php

namespace Entities\Cards\Classes;

use App\Core\AppEntity;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Cards\Classes\Cards;
use Entities\Cards\Models\CardConnectionModel;

class CardConnections extends AppEntity
{
    public $strEntityName       = "Cards";
    public $strDatabaseTable    = "connection_rel";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = CardConnectionModel::class;
    public $strMainModelPrimary = "connection_rel_id";

    public function GetByCardId($intCardId) : ExcellTransaction
    {
        $objTransaction = new ExcellTransaction();

        if (empty($intCardId))
        {
            $objTransaction->Result->Success = false;
            $objTransaction->Result->Count = 0;
            $objTransaction->Result->Message = "You must pass in an id to retrieve a " . $this->strMainModelName . " row.";

            return $objTransaction;
        }

        $objCardModule = new Cards();
        $objCardResult = $objCardModule->getById($intCardId);

        if($objCardResult->Result->Count === 0)
        {
            $objTransaction->Result->Success = false;
            $objTransaction->Result->Count = 0;
            $objTransaction->Result->Message = "No card was found with id " . $intCardId . ".";

            return $objTransaction;
        }

        $objCard = $objCardResult->Data->First();
        $objCard->LoadCardTemplate();
        $intCardConnectionsCount = $objCard->Template->data->connections->count;

        $lstConnectionResult = new ExcellTransaction();

        for($currConnectionCount = 1; $currConnectionCount <= $intCardConnectionsCount; $currConnectionCount++)
        {
            $objConnectionResult = $this->getWhere(["card_id" => $intCardId, "display_order" => $currConnectionCount],"connection_rel_id.DESC",1);
            $lstConnectionResult->Data->Add($objConnectionResult->Data->First()->connection_rel_id, $objConnectionResult->Data->First());
        }

        $lstConnectionResult->Result->Success = true;
        $lstConnectionResult->Result->Count = $lstConnectionResult->Data->Count();

        return  $lstConnectionResult;
    }
}