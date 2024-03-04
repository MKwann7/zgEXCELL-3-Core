<?php

namespace Entities\Contacts\Classes;

use App\Core\AppController;
use App\Core\AppEntity;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Contacts\Models\ContactModel;

class Contacts extends AppEntity
{
    public string $strEntityName       = "Contacts";
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

            $this->lstAppTransaction->result->Success = false;
            $this->lstAppTransaction->result->Count = 0;
            $this->lstAppTransaction->result->Message = "You must pass in an id to retrieve a " . $this->strMainModelName . " row.";

            return $this->lstAppTransaction;
        }

        $objContactUserRelResult = (new ContactCardRels())->getWhere(["card_id" => $intCardId]);

        if ($objContactUserRelResult->result->Count === 0)
        {
            return $objContactUserRelResult;
        }

        $arContactList = $objContactUserRelResult->getData()->FieldsToArray(["contact_id"]);
        $objContactsResult = $this->getWhere(["contact_id", "IN", $arContactList]);

        if ($objContactsResult->result->Count === 0)
        {
            return $objContactsResult;
        }

        $objContactsResult->getData()->MergeFields($objContactUserRelResult->data,["contact_card_rel_id" => "contact_card_rel_id", "mobiniti_contact_id" => "mobiniti_contact_id", "card_id" => "card_id"],["contact_id" => "contact_id"]);

        return $objContactsResult;
    }

    public function GetByUserId($intUserId)
    {
        if (empty($intUserId))
        {
            $this->lstAppTransaction = new ExcellTransaction();

            $this->lstAppTransaction->result->Success = false;
            $this->lstAppTransaction->result->Count = 0;
            $this->lstAppTransaction->result->Message = "You must pass in an id to retrieve a " . $this->strMainModelName . " row.";

            return $this->lstAppTransaction;
        }

        $objContactUserRelResult = (new ContactUserRels())->getWhere(["user_id" => $intUserId]);

        if ($objContactUserRelResult->result->Count === 0)
        {
            return $objContactUserRelResult;
        }

        $arContactList = $objContactUserRelResult->getData()->FieldsToArray(["contact_id"]);

        $objContactsResult = $this->getWhere(["contact_id", "IN", $arContactList]);

        if ($objContactsResult->result->Count === 0)
        {
            return $objContactsResult;
        }

        $objContactsResult->getData()->MergeFields($objContactUserRelResult->data,["contact_user_rel_id" => "contact_user_rel_id", "user_id" => "user_id"],["contact_id" => "contact_id"]);

        return $objContactsResult;
    }
}

