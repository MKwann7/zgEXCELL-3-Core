<?php

namespace Entities\Cards\Classes;

use App\Core\AppController;
use App\Core\AppEntity;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Cards\Models\CardRelModel;
use Entities\Users\Classes\Users;

class CardRels extends AppEntity
{
    public $strEntityName       = "Cards";
    public $strDatabaseTable    = "card_rel";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = CardRelModel::class;
    public $strMainModelPrimary = "card_rel_id";

    public function GetUsersByCardId($intCardId)
    {
        $objCardResult = new ExcellTransaction();

        if ( empty($intCardId) || !isInteger($intCardId) )
        {
            $objCardResult->Result->Success = false;
            $objCardResult->Result->Count   = 0;
            $objCardResult->Result->Message = "You must supply a valid card id.";
            return $objCardResult;
        }

        // TODO - FIX
        //static::Init();
        $colCardRelResult = $this->getWhere("card_id", "=", $intCardId);

        if ($colCardRelResult->Result->Count === 0)
        {
            return $colCardRelResult;
        }

        foreach($colCardRelResult->Data as $currCardRelIndex => $currCardRel)
        {
            $objUserWhereclause[] = ["user_id", "=", $currCardRel->user_id];
            $objUserWhereclause[] = ["OR"];
        }

        array_pop($objUserWhereclause);

        $objUsers = (new Users())->getWhere($objUserWhereclause);

        foreach($colCardRelResult->Data as $currCardRelIndex => $currCardRel)
        {
            $objUser = $objUsers->Data->FindEntityByValue("user_id",$currCardRel->user_id);
            $colCardRelResult->Data->{$currCardRelIndex}->AddUnvalidatedValue("User", $objUser);
        }

        return $colCardRelResult;
    }
}

