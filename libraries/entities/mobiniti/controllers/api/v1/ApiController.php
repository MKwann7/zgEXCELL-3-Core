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

        dd($objMobinitiGroup->getData()->first());
    }

    public function syncMobinitiContactsFromCardId(ExcellHttpModel $objData) : bool
    {
        $inCardId = $objData->Data->Params["id"];

        (new MobinitiGroups())->getById($inCardId)->getData()->Each(function($currGroup)
        {
            $objContactResult = (new MobinitiContactsApiModule())->GetContactsByGroupId($currGroup->id);

            $this->dump("Contacts for Group: " . $objContactResult->Result->Count . " ID: " . $currGroup->id);

            $objContactResult->getData()->Each(function($currContact) use ($currGroup)
            {
                $objContactResult = (new MobinitiContacts())->getWhere(["id" => $currContact->id]);

                if ($objContactResult->result->Count >= 1)
                {
                    $objContact = $objContactResult->getData()->first();

                    $objContactCardRelResult = (new MobinitiContactGroupRels())->getWhere(["mobiniti_contact_id" => $objContact->id, "mobiniti_group_id" => $currGroup->id]);
                    $objContactCardRel = new MobinitiContactGroupRelModel();

                    if ($objContactCardRelResult->result->Count > 0)
                    {
                        $objContactCardRel = $objContactCardRelResult->getData()->first();
                        $objContactCardRel->card_id = $currGroup->card_id;

                        $objContactCardRelCreationResult = (new MobinitiContactGroupRels())->update($objContactCardRel);

                        $objContactCardRel = $objContactCardRelCreationResult->getData()->first();

                        if ($objContactCardRelCreationResult->result->Success === true)
                        {
                            $this->dump("> Updated Contact Card Rel: " . $objContactCardRel->mobiniti_contact_group_rel_id);
                        }
                        else
                        {
                            $this->dump("> Updated Contact Card Rel: [ERROR] " . $objContactCardRelCreationResult->result->Message);
                        }
                    }
                    else
                    {
                        $objContactCardRel->mobiniti_contact_id = $objContact->id;
                        $objContactCardRel->mobiniti_group_id = $currGroup->id;
                        $objContactCardRel->card_id = $currGroup->card_id;
                        $objContactCardRel->created_on = date("Y-m-d H:i:s", strtotime("now"));

                        $objContactCardRelCreationResult = (new MobinitiContactGroupRels())->createNew($objContactCardRel);

                        $objContactCardRel = $objContactCardRelCreationResult->getData()->first();

                        if ($objContactCardRelCreationResult->result->Success === true)
                        {
                            $this->dump("> Adding Contact Card Rel: " . $objContactCardRel->mobiniti_contact_group_rel_id);
                        }
                        else
                        {
                            $this->dump("> Adding Contact Card Rel: [ERROR] " . $objContactCardRelCreationResult->result->Message);
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
                    $objContact = $objContactCreationResult->getData()->first();

                    if ($objContactCreationResult->result->Success === true)
                    {
                        $this->dump(">> Creating Contact: " . $objContact->id);
                    }
                    else
                    {
                        $this->dump(">> Creating Contact: [ERROR] " . $objContactCreationResult->result->Message);
                    }

                    $objContactCardRelResult = (new MobinitiContactGroupRels())->getWhere(["mobiniti_contact_id" => $objContact->id, "mobiniti_group_id" => $currGroup->id]);
                    $objContactCardRel = new MobinitiContactGroupRelModel();

                    if ($objContactCardRelResult->result->Count > 0)
                    {
                        $objContactCardRel = $objContactCardRelResult->getData()->first();
                        $objContactCardRel->card_id = $currGroup->card_id;

                        $objContactCardRelCreationResult = (new MobinitiContactGroupRels())->update($objContactCardRel);

                        $objContactCardRel = $objContactCardRelCreationResult->getData()->first();

                        if ($objContactCardRelCreationResult->result->Success === true)
                        {
                            $this->dump("> Updated Contact Card Rel: " . $objContactCardRel->mobiniti_contact_group_rel_id);
                        }
                        else
                        {
                            $this->dump("> Updated Contact Card Rel: [ERROR] " . $objContactCardRelCreationResult->result->Message);
                        }
                    }
                    else
                    {
                        $objContactCardRel->mobiniti_contact_id = $objContact->id;
                        $objContactCardRel->mobiniti_group_id = $currGroup->id;
                        $objContactCardRel->created_on = date("Y-m-d H:i:s", strtotime("now"));

                        $objContactCardRelCreationResult = (new MobinitiContactGroupRels())->createNew($objContactCardRel);

                        $objContactCardRel = $objContactCardRelCreationResult->getData()->first();

                        if ($objContactCardRelCreationResult->result->Success === true)
                        {
                            $this->dump("> Adding Contact Card Rel: " . $objContactCardRel->mobiniti_contact_group_rel_id);
                        }
                        else
                        {
                            $this->dump("> Adding Contact Card Rel: [ERROR] " . $objContactCardRelCreationResult->result->Message);
                        }
                    }

                    $objCardResult = (new Cards())->getById($currGroup->card_id);

                    if ($objCardResult->result->Count === 0)
                    {
                        return;
                    }

                    $objCard = $objCardResult->getData()->first();

                    $objContactUserRelResult = (new MobinitiContactUserRels())->getWhere(["mobiniti_contact_id" => $objContact->id, "user_id" => $objCard->owner_id]);

                    if ($objContactUserRelResult->result->Count > 0)
                    {
                        return;
                    }

                    $objContactUserRel = new MobinitiContactUserRelModel();
                    $objContactUserRel->user_id = $objCard->owner_id;
                    $objContactUserRel->mobiniti_contact_id = $objContact->id;

                    $objContactUserRelCreationResult = (new MobinitiContactUserRels())->createNew($objContactUserRel);

                    $objContactUserRel = $objContactUserRelCreationResult->getData()->first();

                    if ($objContactUserRelCreationResult->result->Success === true)
                    {
                        $this->dump(">> Adding Contact User Rel: " . $objContactUserRel->mobiniti_contact_user_rel_id);
                    }
                    else
                    {
                        $this->dump(">> Adding Contact User Rel: [ERROR] " . $objContactUserRelCreationResult->result->Message);
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
        $objMobinitiGroup = (new MobinitiGroups())->getById($objMobinitiApiResult->getData()->first()->id)->getData()->first();

        $this->syncMobinitiGroup($objMobinitiGroup, $objMobinitiApiResult);

        $objContactResult = (new MobinitiContactsApiModule())->GetContactsByGroupId($objMobinitiGroup->id);

        dd($objContactResult);
    }

    protected function syncMobinitiGroup(MobinitiGroupModel $currGroup, ExcellTransaction $objMobinitiGroup)
    {
        $objMobinitiGroupModel = $objMobinitiGroup->getData()->FindEntityByValue("id", $currGroup->id);

        /** @var MobinitiGroupModel $objMobinitiGroupModel */
        if ($objMobinitiGroupModel !== null)
        {
            $objMobinitiGroupModel->name = $currGroup->name;
            $objMobinitiGroupModel->join_message = $currGroup->join_message;
            $objMobinitiGroupModel->one_time_message = $currGroup->one_time_message === true ? EXCELL_TRUE : EXCELL_FALSE;
            $objMobinitiGroupModel->always_send_join = $currGroup->always_send_join === true ? EXCELL_TRUE : EXCELL_FALSE;
            $objMobinitiGroupModel->always_send_optin = $currGroup->always_send_optin === true ? EXCELL_TRUE : EXCELL_FALSE;
            $objMobinitiGroupModel->social_profiling = $currGroup->social_profiling;
            $objMobinitiGroupModel->email_new_contact = $currGroup->email_new_contact;
            $objMobinitiGroupModel->emails = $currGroup->emails;
            $objMobinitiGroupModel->updated_at = $currGroup->updated_at;
            $objMobinitiGroupModel->optin = $currGroup->optin === true ? EXCELL_TRUE : EXCELL_FALSE;
            $objMobinitiGroupModel->status = $currGroup->status;

            $objMobinitiGroupResult = (new MobinitiGroups())->update($objMobinitiGroupModel);

            if ($objMobinitiGroupResult->result->Success === true)
            {
                $this->dump("> Updating Mobiniti Group: " . $objMobinitiGroupResult->getData()->first()->id);
            }
            else
            {
                $this->dump("> Updating Mobiniti Group: [ERROR] " . $objMobinitiGroupResult->result->Message);
            }

            $this->syncMobinitiGroupWithCards($currGroup);

            return;
        }

        $objMobinitiGroupModel = new MobinitiGroupModel();
        $objMobinitiGroupModel->id = $currGroup->id;
        $objMobinitiGroupModel->name = $currGroup->name;
        $objMobinitiGroupModel->join_message = $currGroup->join_message;
        $objMobinitiGroupModel->one_time_message = $currGroup->one_time_message === true ? EXCELL_TRUE : EXCELL_FALSE;
        $objMobinitiGroupModel->always_send_join = $currGroup->always_send_join === true ? EXCELL_TRUE : EXCELL_FALSE;
        $objMobinitiGroupModel->always_send_optin = $currGroup->always_send_optin === true ? EXCELL_TRUE : EXCELL_FALSE;
        $objMobinitiGroupModel->social_profiling = $currGroup->social_profiling;
        $objMobinitiGroupModel->email_new_contact = $currGroup->email_new_contact;
        $objMobinitiGroupModel->emails = $currGroup->emails;
        $objMobinitiGroupModel->updated_at = $currGroup->updated_at;
        $objMobinitiGroupModel->optin = $currGroup->optin === true ? EXCELL_TRUE : EXCELL_FALSE;
        $objMobinitiGroupModel->status = $currGroup->status;

        $objMobinitiGroupResult = (new MobinitiGroups())->createNew($objMobinitiGroupModel);

        if ($objMobinitiGroupResult->result->Success === true)
        {
            $this->dump("> Adding Mobiniti Group: " . $objMobinitiGroupResult->getData()->first()->id);
        }
        else
        {
            $this->dump("> Adding Mobiniti Group: [ERROR] " . $objMobinitiGroupResult->result->Message);
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

                if ($objCardResult->result->Count === 0)
                {
                    $objCardResult = (new Cards())->getWhere(["card_keyword" => $strCardKeyword]);
                }

                $this->dump($objCardResult);

                if ($objCardResult->result->Success === true)
                {
                    $this->dump("Linking [{$currGroup->id}] To Card Num: " . $objCardResult->getData()->first()->card_id);

                    $currGroup->card_id = $objCardResult->getData()->first()->card_id;
                    $objGroupUpdateResult = (new MobinitiGroups())->update($currGroup);

                    if($objGroupUpdateResult->result->Success !== true)
                    {
                        echo $objGroupUpdateResult->result->Message . PHP_EOL;
                        echo $objGroupUpdateResult->result->Query . PHP_EOL;
                        print_r($objGroupUpdateResult->result->Errors); echo PHP_EOL;
                    }
                }
            }
            else
            {
                $strGroupNameId = explode(" ",$currGroup->name)[0];

                if (isInteger($strGroupNameId))
                {
                    $objCardResult = (new Cards())->getWhere(["card_num" => $strGroupNameId]);

                    if ($objCardResult->result->Success === true)
                    {
                        $this->dump("Linking [{$currGroup->id}] To Card Num: " . $objCardResult->getData()->first()->card_id);

                        $currGroup->card_id = $objCardResult->getData()->first()->card_id;
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
