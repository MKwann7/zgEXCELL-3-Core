<?php

namespace Entities\Users\Classes;

use App\Core\AppController;
use App\Core\AppEntity;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Cards\Classes\Cards;
use Entities\Cards\Classes\CardRels;
use Entities\Users\Models\UserClassModel;

class UserClass extends AppEntity
{
    public $strEntityName       = "Users";
    public $strDatabaseTable    = "user_class";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = UserClassModel::class;
    public $strMainModelPrimary = "user_class_id";

    public function GetRoleByCardId($intCardId) : ExcellTransaction
    {
        $objCardResult = new ExcellTransaction();

        if(!isInteger($intCardId))
        {
            $objCardResult->Result->Success = false;
            $objCardResult->Result->Count = 0;
            $objCardResult->Result->Message = "You must supply a card id.";
            return $objCardResult;
        }

        $lstCardResult = (new Cards())->getById($intCardId);

        if ($lstCardResult->Result->Success === false || $lstCardResult->Result->Count === 0)
        {
            $objCardResult->Result->Success = false;
            $objCardResult->Result->Count = 0;
            $objCardResult->Result->Message = "No card was found with ID of {$intCardId}.";
            $objCardResult->Result->Trace = trace();
            return $objCardResult;
        }

        $lstCardRelTypeResult = (new Cards())->GetCardRelTypes();
        $objCardRel = (new CardRels())->getWhere(["card_id" => $intCardId]);
        $objCardOwner = (new Users())->GetCardOwnerByCardId($intCardId);
        $arCardUserId = $objCardRel->Data->FieldsToArray(["user_id"]);
        $arCardUserId[] = $objCardOwner->Data->First()->user_id;

        $objUsers = (new Users())->getWhereIn("user_id", $arCardUserId);

        $objCardRel->Data->MergeFields($objUsers->Data,["first_name","last_name","username","preferred_name"],["user_id"]);
        $objCardRel->Data->MergeFields($lstCardRelTypeResult->Data,["name" => "role","card_rel_permissions"],["card_rel_type_id"]);
        $objCardRel->Data->Add($objCardOwner->Data->First());

        return $objCardRel;
    }
}