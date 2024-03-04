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
    public string $strEntityName       = "Users";
    public $strDatabaseTable    = "user_class";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = UserClassModel::class;
    public $strMainModelPrimary = "user_class_id";

    public function GetRoleByCardId($intCardId) : ExcellTransaction
    {
        $objCardResult = new ExcellTransaction();

        if(!isInteger($intCardId))
        {
            $objCardResult->result->Success = false;
            $objCardResult->result->Count = 0;
            $objCardResult->result->Message = "You must supply a card id.";
            return $objCardResult;
        }

        $lstCardResult = (new Cards())->getById($intCardId);

        if ($lstCardResult->result->Success === false || $lstCardResult->result->Count === 0)
        {
            $objCardResult->result->Success = false;
            $objCardResult->result->Count = 0;
            $objCardResult->result->Message = "No card was found with ID of {$intCardId}.";
            $objCardResult->result->Trace = trace();
            return $objCardResult;
        }

        $lstCardRelTypeResult = (new Cards())->GetCardRelTypes();
        $objCardRel = (new CardRels())->getWhere(["card_id" => $intCardId]);
        $objCardOwner = (new Users())->GetCardOwnerByCardId($intCardId);
        $arCardUserId = $objCardRel->getData()->FieldsToArray(["user_id"]);
        $arCardUserId[] = $objCardOwner->getData()->first()->user_id;

        $objUsers = (new Users())->getWhereIn("user_id", $arCardUserId);

        $objCardRel->getData()->MergeFields($objUsers->data,["first_name","last_name","username","preferred_name"],["user_id"]);
        $objCardRel->getData()->MergeFields($lstCardRelTypeResult->data,["name" => "role","card_rel_permissions"],["card_rel_type_id"]);
        $objCardRel->getData()->Add($objCardOwner->getData()->first());

        return $objCardRel;
    }
}