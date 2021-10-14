<?php

namespace Entities\Contacts\Classes;

use App\Core\AppController;
use App\Core\AppEntity;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Contacts\Models\ContactModel;

class Contacts extends AppEntity
{
    public $strEntityName       = "Contacts";
    public $strDatabaseTable    = "contact";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = ContactModel::class;
    public $strMainModelPrimary = "contact_id";
    public $isPrimaryModule     = true;

    public function GetByCardId($intCardId)
    {
        if (empty($intCardId))
        {
            $this->lstAppTransaction = new ExcellTransaction();

            $this->lstAppTransaction->Result->Success = false;
            $this->lstAppTransaction->Result->Count = 0;
            $this->lstAppTransaction->Result->Message = "You must pass in an id to retrieve a " . $this->strMainModelName . " row.";

            return $this->lstAppTransaction;
        }

        $objContactUserRelResult = (new ContactCardRels())->getWhere(["card_id" => $intCardId]);

        if ($objContactUserRelResult->Result->Count === 0)
        {
            return $objContactUserRelResult;
        }

        $arContactList = $objContactUserRelResult->Data->FieldsToArray(["contact_id"]);
        $objContactsResult = $this->getWhere(["contact_id", "IN", $arContactList]);

        if ($objContactsResult->Result->Count === 0)
        {
            return $objContactsResult;
        }

        $objContactsResult->Data->MergeFields($objContactUserRelResult->Data,["contact_card_rel_id" => "contact_card_rel_id", "mobiniti_contact_id" => "mobiniti_contact_id", "card_id" => "card_id"],["contact_id" => "contact_id"]);

        return $objContactsResult;
    }

    public function GetByUserId($intUserId)
    {
        if (empty($intUserId))
        {
            $this->lstAppTransaction = new ExcellTransaction();

            $this->lstAppTransaction->Result->Success = false;
            $this->lstAppTransaction->Result->Count = 0;
            $this->lstAppTransaction->Result->Message = "You must pass in an id to retrieve a " . $this->strMainModelName . " row.";

            return $this->lstAppTransaction;
        }

        $objContactUserRelResult = (new ContactUserRels())->getWhere(["user_id" => $intUserId]);

        if ($objContactUserRelResult->Result->Count === 0)
        {
            return $objContactUserRelResult;
        }

        $arContactList = $objContactUserRelResult->Data->FieldsToArray(["contact_id"]);

        $objContactsResult = $this->getWhere(["contact_id", "IN", $arContactList]);

        if ($objContactsResult->Result->Count === 0)
        {
            return $objContactsResult;
        }

        $objContactsResult->Data->MergeFields($objContactUserRelResult->Data,["contact_user_rel_id" => "contact_user_rel_id", "user_id" => "user_id"],["contact_id" => "contact_id"]);

        return $objContactsResult;
    }
}

