<?php

namespace App\Mobiniti\Controllers\Api\V1;

use App\Utilities\Excell\ExcellHttpModel;
use App\Utilities\Transaction\ExcellTransaction;
use MobinitiContactsApiModule;
use MobinitiGroupsApiModule;
use Entities\Cards\Classes\Cards;
use Entities\Mobiniti\Classes\Base\MobinitiController;
use Entities\Mobiniti\Classes\MobinitiContactGroupRels;
use Entities\Mobiniti\Classes\MobinitiContacts;
use Entities\Mobiniti\Classes\MobinitiContactUserRels;
use Entities\Mobiniti\Classes\MobinitiGroups;
use Entities\Mobiniti\Models\MobinitiContactGroupRelModel;
use Entities\Mobiniti\Models\MobinitiContactModel;
use Entities\Mobiniti\Models\MobinitiContactUserRelModel;
use Entities\Mobiniti\Models\MobinitiGroupModel;

class ApiController extends MobinitiController
{
    public function GetMobinitiGroupForDebugging(ExcellHttpModel $objData) : bool
    {
        $blnCylce = true;
        $intPageOffset = 1;
        $objMobinitiGroupModule = new MobinitiGroupsApiModule();
        $objMobinitiGroup = $objMobinitiGroupModule->getAll(10);

        dd($objMobinitiGroup->Data->First());
    }

    public function syncMobinitiContactsFromCardId(ExcellHttpModel $objData) : bool
    {
        $inCardId = $objData->Data->Params["id"];

        (new MobinitiGroups())->getById($inCardId)->Data->Each(function($currGroup)
        {
            $objContactResult = (new MobinitiContactsApiModule())->GetContactsByGroupId($currGroup->id);

            $this->dump("Contacts for Group: " . $objContactResult->Result->Count . " ID: " . $currGroup->id);

            $objContactResult->Data->Each(function($currContact) use ($currGroup)
            {
                $objContactResult = (new MobinitiContacts())->getWhere(["id" => $currContact->id]);

                if ($objContactResult->Result->Count >= 1)
                {
                    $objContact = $objContactResult->Data->First();

                    $objContactCardRelResult = (new MobinitiContactGroupRels())->getWhere(["mobiniti_contact_id" => $objContact->id, "mobiniti_group_id" => $currGroup->id]);
                    $objContactCardRel = new MobinitiContactGroupRelModel();

                    if ($objContactCardRelResult->Result->Count > 0)
                    {
                        $objContactCardRel = $objContactCardRelResult->Data->First();
                        $objContactCardRel->card_id = $currGroup->card_id;

                        $objContactCardRelCreationResult = (new MobinitiContactGroupRels())->update($objContactCardRel);

                        $objContactCardRel = $objContactCardRelCreationResult->Data->First();

                        if ($objContactCardRelCreationResult->Result->Success === true)
                        {
                            $this->dump("> Updated Contact Card Rel: " . $objContactCardRel->mobiniti_contact_group_rel_id);
                        }
                        else
                        {
                            $this->dump("> Updated Contact Card Rel: [ERROR] " . $objContactCardRelCreationResult->Result->Message);
                        }
                    }
                    else
                    {
                        $objContactCardRel->mobiniti_contact_id = $objContact->id;
                        $objContactCardRel->mobiniti_group_id = $currGroup->id;
                        $objContactCardRel->card_id = $currGroup->card_id;
                        $objContactCardRel->created_on = date("Y-m-d H:i:s", strtotime("now"));

                        $objContactCardRelCreationResult = (new MobinitiContactGroupRels())->createNew($objContactCardRel);

                        $objContactCardRel = $objContactCardRelCreationResult->Data->First();

                        if ($objContactCardRelCreationResult->Result->Success === true)
                        {
                            $this->dump("> Adding Contact Card Rel: " . $objContactCardRel->mobiniti_contact_group_rel_id);
                        }
                        else
                        {
                            $this->dump("> Adding Contact Card Rel: [ERROR] " . $objContactCardRelCreationResult->Result->Message);
                        }
                    }
                }
                else
                {
                    $objContactModel = new MobinitiContactModel();
                    $objContactModel->id = $currContact->id;
                    $objContactModel->first_name = $currContact->first_name;
                    $objContactModel->last_name = $currContact->last_name;
                    $objContactModel->phone_number = $currContact->phone_number;
                    $objContactModel->email = $currContact->email;
                    $objContactModel->birth_date = $currContact->birth_date;
                    $objContactModel->reward_points = $currContact->reward_points;
                    $objContactModel->created_at = $currContact->created_at;
                    $objContactModel->updated_at = $currContact->updated_at;
                    $objContactModel->country_code = $currContact->country_code;

                    $objContactCreationResult = (new MobinitiContacts())->createNew($objContactModel);
                    $objContact = $objContactCreationResult->Data->First();

                    if ($objContactCreationResult->Result->Success === true)
                    {
                        $this->dump(">> Creating Contact: " . $objContact->id);
                    }
                    else
                    {
                        $this->dump(">> Creating Contact: [ERROR] " . $objContactCreationResult->Result->Message);
                    }

                    $objContactCardRelResult = (new MobinitiContactGroupRels())->getWhere(["mobiniti_contact_id" => $objContact->id, "mobiniti_group_id" => $currGroup->id]);
                    $objContactCardRel = new MobinitiContactGroupRelModel();

                    if ($objContactCardRelResult->Result->Count > 0)
                    {
                        $objContactCardRel = $objContactCardRelResult->Data->First();
                        $objContactCardRel->card_id = $currGroup->card_id;

                        $objContactCardRelCreationResult = (new MobinitiContactGroupRels())->update($objContactCardRel);

                        $objContactCardRel = $objContactCardRelCreationResult->Data->First();

                        if ($objContactCardRelCreationResult->Result->Success === true)
                        {
                            $this->dump("> Updated Contact Card Rel: " . $objContactCardRel->mobiniti_contact_group_rel_id);
                        }
                        else
                        {
                            $this->dump("> Updated Contact Card Rel: [ERROR] " . $objContactCardRelCreationResult->Result->Message);
                        }
                    }
                    else
                    {
                        $objContactCardRel->mobiniti_contact_id = $objContact->id;
                        $objContactCardRel->mobiniti_group_id = $currGroup->id;
                        $objContactCardRel->created_on = date("Y-m-d H:i:s", strtotime("now"));

                        $objContactCardRelCreationResult = (new MobinitiContactGroupRels())->createNew($objContactCardRel);

                        $objContactCardRel = $objContactCardRelCreationResult->Data->First();

                        if ($objContactCardRelCreationResult->Result->Success === true)
                        {
                            $this->dump("> Adding Contact Card Rel: " . $objContactCardRel->mobiniti_contact_group_rel_id);
                        }
                        else
                        {
                            $this->dump("> Adding Contact Card Rel: [ERROR] " . $objContactCardRelCreationResult->Result->Message);
                        }
                    }

                    $objCardResult = (new Cards())->getById($currGroup->card_id);

                    if ($objCardResult->Result->Count === 0)
                    {
                        return;
                    }

                    $objCard = $objCardResult->Data->First();

                    $objContactUserRelResult = (new MobinitiContactUserRels())->getWhere(["mobiniti_contact_id" => $objContact->id, "user_id" => $objCard->owner_id]);

                    if ($objContactUserRelResult->Result->Count > 0)
                    {
                        return;
                    }

                    $objContactUserRel = new MobinitiContactUserRelModel();
                    $objContactUserRel->user_id = $objCard->owner_id;
                    $objContactUserRel->mobiniti_contact_id = $objContact->id;

                    $objContactUserRelCreationResult = (new MobinitiContactUserRels())->createNew($objContactUserRel);

                    $objContactUserRel = $objContactUserRelCreationResult->Data->First();

                    if ($objContactUserRelCreationResult->Result->Success === true)
                    {
                        $this->dump(">> Adding Contact User Rel: " . $objContactUserRel->mobiniti_contact_user_rel_id);
                    }
                    else
                    {
                        $this->dump(">> Adding Contact User Rel: [ERROR] " . $objContactUserRelCreationResult->Result->Message);
                    }
                }
            });
        });

        die("done!");
    }
    public function getMobinitiContactsFromCardId(ExcellHttpModel $objData) : bool
    {
        $inCardId = $objData->Data->Params["id"];

        $objMobinitiApiResult = (new MobinitiGroupsApiModule())->getById($inCardId);
        $objMobinitiGroup = (new MobinitiGroups())->getById($objMobinitiApiResult->Data->First()->id)->Data->First();

        $this->syncMobinitiGroup($objMobinitiGroup, $objMobinitiApiResult);

        $objContactResult = (new MobinitiContactsApiModule())->GetContactsByGroupId($objMobinitiGroup->id);

        dd($objContactResult);
    }

    protected function syncMobinitiGroup(MobinitiGroupModel $currGroup, ExcellTransaction $objMobinitiGroup)
    {
        $objMobinitiGroupModel = $objMobinitiGroup->Data->FindEntityByValue("id", $currGroup->id);

        /** @var MobinitiGroupModel $objMobinitiGroupModel */
        if ($objMobinitiGroupModel !== null)
        {
            $objMobinitiGroupModel->name = $currGroup->name;
            $objMobinitiGroupModel->join_message = $currGroup->join_message;
            $objMobinitiGroupModel->one_time_message = $currGroup->one_time_message === true ? ExcellTrue : ExcellFalse;
            $objMobinitiGroupModel->always_send_join = $currGroup->always_send_join === true ? ExcellTrue : ExcellFalse;
            $objMobinitiGroupModel->always_send_optin = $currGroup->always_send_optin === true ? ExcellTrue : ExcellFalse;
            $objMobinitiGroupModel->social_profiling = $currGroup->social_profiling;
            $objMobinitiGroupModel->email_new_contact = $currGroup->email_new_contact;
            $objMobinitiGroupModel->emails = $currGroup->emails;
            $objMobinitiGroupModel->updated_at = $currGroup->updated_at;
            $objMobinitiGroupModel->optin = $currGroup->optin === true ? ExcellTrue : ExcellFalse;
            $objMobinitiGroupModel->status = $currGroup->status;

            $objMobinitiGroupResult = (new MobinitiGroups())->update($objMobinitiGroupModel);

            if ($objMobinitiGroupResult->Result->Success === true)
            {
                $this->dump("> Updating Mobiniti Group: " . $objMobinitiGroupResult->Data->First()->id);
            }
            else
            {
                $this->dump("> Updating Mobiniti Group: [ERROR] " . $objMobinitiGroupResult->Result->Message);
            }

            $this->syncMobinitiGroupWithCards($currGroup);

            return;
        }

        $objMobinitiGroupModel = new MobinitiGroupModel();
        $objMobinitiGroupModel->id = $currGroup->id;
        $objMobinitiGroupModel->name = $currGroup->name;
        $objMobinitiGroupModel->join_message = $currGroup->join_message;
        $objMobinitiGroupModel->one_time_message = $currGroup->one_time_message === true ? ExcellTrue : ExcellFalse;
        $objMobinitiGroupModel->always_send_join = $currGroup->always_send_join === true ? ExcellTrue : ExcellFalse;
        $objMobinitiGroupModel->always_send_optin = $currGroup->always_send_optin === true ? ExcellTrue : ExcellFalse;
        $objMobinitiGroupModel->social_profiling = $currGroup->social_profiling;
        $objMobinitiGroupModel->email_new_contact = $currGroup->email_new_contact;
        $objMobinitiGroupModel->emails = $currGroup->emails;
        $objMobinitiGroupModel->updated_at = $currGroup->updated_at;
        $objMobinitiGroupModel->optin = $currGroup->optin === true ? ExcellTrue : ExcellFalse;
        $objMobinitiGroupModel->status = $currGroup->status;

        $objMobinitiGroupResult = (new MobinitiGroups())->createNew($objMobinitiGroupModel);

        if ($objMobinitiGroupResult->Result->Success === true)
        {
            $this->dump("> Adding Mobiniti Group: " . $objMobinitiGroupResult->Data->First()->id);
        }
        else
        {
            $this->dump("> Adding Mobiniti Group: [ERROR] " . $objMobinitiGroupResult->Result->Message);
        }

        $this->syncMobinitiGroupWithCards($currGroup);
    }

    protected function syncMobinitiGroupWithCards($currGroup)
    {
        try
        {
            if (strpos(strtolower($currGroup->join_message),"ezcard.com") !== false)
            {
                $arJoinMessage = explode("/", $currGroup->join_message);
                $arJoinMessage = array_reverse($arJoinMessage);
                $strCardKeyword = str_replace("?", "", $arJoinMessage[0]);
                $strCardKeyword = preg_replace("/[^A-Za-z0-9 ]/", '', $strCardKeyword);

                $this->dump($strCardKeyword);

                $objCardResult = (new Cards())->getWhere(["card_num" => $strCardKeyword]);

                if ($objCardResult->Result->Count === 0)
                {
                    $objCardResult = (new Cards())->getWhere(["card_keyword" => $strCardKeyword]);
                }

                $this->dump($objCardResult);

                if ($objCardResult->Result->Success === true)
                {
                    $this->dump("Linking [{$currGroup->id}] To Card Num: " . $objCardResult->Data->First()->card_id);

                    $currGroup->card_id = $objCardResult->Data->First()->card_id;
                    $objGroupUpdateResult = (new MobinitiGroups())->update($currGroup);

                    if($objGroupUpdateResult->Result->Success !== true)
                    {
                        echo $objGroupUpdateResult->Result->Message . PHP_EOL;
                        echo $objGroupUpdateResult->Result->Query . PHP_EOL;
                        print_r($objGroupUpdateResult->Result->Errors); echo PHP_EOL;
                    }
                }
            }
            else
            {
                $strGroupNameId = explode(" ",$currGroup->name)[0];

                if (isInteger($strGroupNameId))
                {
                    $objCardResult = (new Cards())->getWhere(["card_num" => $strGroupNameId]);

                    if ($objCardResult->Result->Success === true)
                    {
                        $this->dump("Linking [{$currGroup->id}] To Card Num: " . $objCardResult->Data->First()->card_id);

                        $currGroup->card_id = $objCardResult->Data->First()->card_id;
                        $objGroupUpdateResult = (new MobinitiGroups())->update($currGroup);
                    }
                }
            }
        }
        catch(\Exception $ex)
        {
            $this->dump($ex->getMessage());
            $this->dump($ex);
        }
    }

    protected function dump($string)
    {
        dump($string);
    }
}
