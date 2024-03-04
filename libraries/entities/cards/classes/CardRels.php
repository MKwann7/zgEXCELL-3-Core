<?php

namespace Entities\Cards\Classes;

use App\Core\AppController;
use App\Core\AppEntity;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Cards\Models\CardRelModel;
use Entities\Users\Classes\Users;

class CardRels extends AppEntity
{
    public string $strEntityName       = "Cards";
    public $strDatabaseTable    = "card_rel";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = CardRelModel::class;
    public $strMainModelPrimary = "card_rel_id";

    public function GetUsersByCardId($intCardId)
    {
        $objCardResult = new ExcellTransaction();

        if ( empty($intCardId) || !isInteger($intCardId) )
        {
            $objCardResult->result->Success = false;
            $objCardResult->result->Count   = 0;
            $objCardResult->result->Message = "You must supply a valid card id.";
            return $objCardResult;
        }

        // TODO - FIX
        //static::Init();
        $colCardRelResult = $this->getWhere("card_id", "=", $intCardId);

        if ($colCardRelResult->result->Count === 0)
        {
            return $colCardRelResult;
        }

        foreach($colCardRelResult->data as $currCardRelIndex => $currCardRel)
        {
            $objUserWhereclause[] = ["user_id", "=", $currCardRel->user_id];
            $objUserWhereclause[] = ["OR"];
        }

        array_pop($objUserWhereclause);

        $objUsers = (new Users())->getWhere($objUserWhereclause);

        foreach($colCardRelResult->data as $currCardRelIndex => $currCardRel)
        {
            $objUser = $objUsers->getData()->FindEntityByValue("user_id",$currCardRel->user_id);
            $colCardRelResult->getData()->{$currCardRelIndex}->AddUnvalidatedValue("User", $objUser);
        }

        return $colCardRelResult;
    }
}

