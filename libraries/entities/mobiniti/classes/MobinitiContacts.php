<?php

namespace Entities\Mobiniti\Classes;

use App\Core\AppEntity;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Mobiniti\Models\MobinitiContactModel;

class MobinitiContacts extends AppEntity
{
    public string $strEntityName       = "Mobiniti";
    public $strDatabaseTable    = "mobiniti_contact";
    public $strMainModelName    = MobinitiContactModel::class;
    public $strMainModelPrimary = "id";
    public $strDatabaseName     = "Main";

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

        $objContactUserRelResult = (new MobinitiContactGroupRels())->getWhere(["card_id" => $intCardId]);

        if ($objContactUserRelResult->result->Count === 0)
        {
            return $objContactUserRelResult;
        }

        $arContactList = $objContactUserRelResult->getData()->FieldsToArray(["mobiniti_contact_id"]);
        $objContactsResult = $this->getWhere(["id", "IN", $arContactList]);

        if ($objContactsResult->result->Count === 0)
        {
            return $objContactsResult;
        }

        $objContactsResult->getData()->MergeFields($objContactUserRelResult->data,["card_id" => "card_id", "created_on" => "created_on", "mobiniti_contact_group_rel_id" => "contact_id"],["mobiniti_contact_id" => "id"]);

        return $objContactsResult;
    }
}