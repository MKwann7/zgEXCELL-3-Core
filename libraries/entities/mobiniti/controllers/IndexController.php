<?php

namespace App\Mobiniti\Controllers;

use App\Utilities\Excell\ExcellHttpModel;
use Entities\Cards\Classes\Cards;
use Entities\Contacts\Classes\ContactCardRels;
use Entities\Contacts\Classes\Contacts;
use Entities\Contacts\Classes\ContactUserRels;
use Entities\Contacts\Models\ContactCardRelModel;
use Entities\Contacts\Models\ContactModel;
use Entities\Contacts\Models\ContactUserRelModel;
use Entities\Mobiniti\Classes\Base\MobinitiController;
use Entities\Mobiniti\Classes\MobinitiGroups;
use Entities\Mobiniti\Models\MobinitiContactModel;
use Entities\Mobiniti\Models\MobinitiGroupModel;
use Vendors\Mobiniti\Main\V100\Classes\MobinitiContactsApiModule;

class IndexController extends MobinitiController
{
    public function index(ExcellHttpModel $objData) : bool
    {
        $objGroupResult = (new MobinitiGroups())->getAll();

        /** @var MobinitiGroupModel $currGroup */
        foreach($objGroupResult->Data as $currGroup)
        {
            if (empty($currGroup->card_id))
            {
                continue;
            }

            dump($currGroup);
        }

        return true;
    }

    public function GetMobinitiGroupById(ExcellHttpModel $objData) : bool
    {
        $strGroupId = $objData->Data->Params["id"];

        $blnCylce = true;
        $intPageOffset = 1;
        $objMobinitiGroup = (new MobinitiGroups())->getById($strGroupId);
        $objMobinitiGroupApiResult = (new MobinitiGroupsApiModule())->getById($strGroupId);
        $objMobinitiGroupResult = (new MobinitiGroups())->update($objMobinitiGroupApiResult->Data->First());

        $this->dump("STARTING WHILE LOOP");

        try
        {
            while($blnCylce === true)
            {
                $objMobinitiApiResult = (new MobinitiGroupsApiModule())->getAll(100,$intPageOffset);

                $this->dump("Batch Start [{$intPageOffset}] Count = " . $objMobinitiApiResult->Data->Count());

                if (($objMobinitiApiResult->Result->Count + $objMobinitiApiResult->Result->Depth) < $objMobinitiApiResult->Result->Total)
                {
                    $intPageOffset++;
                }
                else
                {
                    $blnCylce = false;
                }

                $intCount = 0;

                $objMobinitiApiResult->Data->Each(function(MobinitiGroupModel $currGroup, $currIndex) use ($objMobinitiGroup, &$intCount, $strGroupId)
                {
                    if (trim(strtolower($currGroup->id)) === $strGroupId)
                    {
                        dump($currGroup);
                    }
                    else
                    {
                        //$this->dump("not in: " .  $currGroup->id . " " . $currGroup->name);
                    }

                    $intCount++;
                });

                $this->dump("Total Processed: " . $intCount);
            }
        }
        catch(\Exception $ex)
        {
            $this->dump($ex->getMessage());
            $this->dump($ex);
        }

        return true;
    }

    protected function dump($string)
    {
        echo($string . PHP_EOL);
    }

    public function SyncMobinitiContactsWithCards() : bool
    {
        ini_set('memory_limit', '-1');
        set_time_limit(300);

        $objContactResult = (new Contacts())->getAll(1000,1);

        $objContactResult->Data->Each(function($currContact)
        {
            $colMobinitiContact = (new MobinitiContactsApiModule())->getById($currContact->mobiniti_id);

            if (empty($colMobinitiContact->Data->First()->groups))
            {
                return;
            }

            foreach($colMobinitiContact->Data->First()->groups as $currGroup)
            {
                $objMobinitiGroupResult = (new MobinitiGroups())->getById($currGroup->id);

                if ($objMobinitiGroupResult->Result->Count === 0 || empty($objMobinitiGroupResult->Data->First()->card_id))
                {
                    continue;
                }

                $objContactCardRelResult = (new ContactCardRels())->getWhere(["contact_id" => $currContact->contact_id, "card_id" => $objMobinitiGroupResult->Data->First()->card_id]);
                $objContactCardRel = new ContactCardRelModel();

                if ($objContactCardRelResult->Result->Count > 0)
                {
                    $objContactCardRel = $objContactCardRelResult->Data->First();
                }
                else
                {
                    $objContactCardRel = new ContactCardRelModel();
                    $objContactCardRel->contact_id = $currContact->contact_id;
                    $objContactCardRel->card_id = $currGroup->card_id;
                    $objContactCardRel->mobiniti_contact_id = $currContact->mobiniti_id;
                    $objContactCardRel->mobiniti_group_id = $currGroup->id;

                    $objContactCardRelCreationResult = (new ContactCardRels())->createNew($objContactCardRel);

                    $objContactCardRel = $objContactCardRelCreationResult->Data->First();
                }
            }
        });

        dd("DONE!");
    }

    public function SyncMobinitiContactsWithContacts() : bool
    {
        ini_set('memory_limit', '-1');
        set_time_limit(300);

        $blnCylce = true;
        $intPageOffset = 1;

        while($blnCylce === true)
        {
            $colMobinitiContact = (new MobinitiContactsApiModule())->getAll(100,$intPageOffset);

            if (($colMobinitiContact->Result->Count + $colMobinitiContact->Result->Depth) < $colMobinitiContact->Result->Total)
            {
                $intPageOffset++;
            }
            else
            {
                $blnCylce = false;
            }

            flush();

            $colMobinitiContact->Data->Each(function(MobinitiContactModel $currContact) {

                $objContactResult = (new Contacts())->getWhere(["mobiniti_id" => $currContact->id]);
                $objContactModel = new ContactModel();

                if ($objContactResult->Result->Count > 0)
                {
                    $objContact = $objContactResult->Data->First();
                }
                else
                {
                    $objContactModel->company_id = 0;
                    $objContactModel->division_id = 0;
                    $objContactModel->user_id = 0;
                    $objContactModel->first_name = $currContact->first_name;
                    $objContactModel->last_name = $currContact->last_name;
                    $objContactModel->phone = $currContact->phone_number;
                    $objContactModel->email = $currContact->email;
                    $objContactModel->birth_date = $currContact->birth_date;
                    $objContactModel->mobiniti_id = $currContact->id;

                    $objContactCreationResult = (new Contacts())->createNew($objContactModel);
                    $objContact = $objContactCreationResult->Data->First();
                }

                if(!empty($currContact->groups))
                {
                    $currContact->groups->Each(function(MobinitiGroupModel $currGroup) use ($objContact) {

                        $objGroupResult = (new MobinitiGroups())->getById($currGroup->id);

                        if ($objGroupResult->Result->Count === 0)
                        {
                            return;
                        }

                        $objGroup = $objGroupResult->Data->First();

                        if (empty($objContact->contact_id))
                        {
                            return;
                        }

                        if (empty($objGroup->card_id))
                        {
                            return;
                        }

                        $objContactCardRelResult = (new ContactCardRels())->getWhere(["mobiniti_group_id" => $objGroup->id, "contact_id" => $objContact->contact_id, "card_id" => $objGroup->card_id]);
                        $objContactCardRel = new ContactCardRelModel();

                        if ($objContactCardRelResult->Result->Count > 0)
                        {
                            $objContactCardRel = $objContactCardRelResult->Data->First();
                        }
                        else
                        {
                            $objContactCardRel = new ContactCardRelModel();
                            $objContactCardRel->contact_id = $objContact->contact_id;
                            $objContactCardRel->card_id = $objGroup->card_id;
                            $objContactCardRel->mobiniti_contact_id = $objContact->mobiniti_id;
                            $objContactCardRel->mobiniti_group_id = $objGroup->id;

                            $objContactCardRelCreationResult = (new ContactCardRels())->createNew($objContactCardRel);

                            $objContactCardRel = $objContactCardRelCreationResult->Data->First();
                        }

                        $objCardResult = (new Cards())->getById($objGroup->card_id);

                        if ($objCardResult->Result->Count === 0)
                        {
                            return;
                        }

                        $objCard = $objCardResult->Data->First();

                        $objContactUserRelResult = (new ContactUserRels())->getWhere(["contact_id" => $objContact->contact_id, "user_id" => $objCard->user_id]);

                        if ($objContactUserRelResult->Result->Count > 0)
                        {
                            return;
                        }

                        $objContactUserRel = new ContactUserRelModel();
                        $objContactUserRel->contact_id = $objContact->contact_id;
                        $objContactUserRel->user_id = $objCard->owner_id;
                        $objContactUserRel->mobiniti_contact_id = $objContact->mobiniti_id;

                        $objContactUserRelCreationResult = (new ContactUserRels())->createNew($objContactUserRel);
                    });
                }
            });
        }

        return true;
    }

    public function SyncMobinitiGroupsWithCards() : bool
    {
        //$objGroupResult = (new MobinitiGroupsApiModule(MobinitiToken))->getAll();
        $objGroupResult = (new MobinitiGroups())->getWhere(["card_id" => ExcellNull]);

        if ($objGroupResult->Result->Count === 0)
        {
            dd("No Groups Found.");
        }

        /** @var MobinitiGroupModel $currGroup */
        foreach($objGroupResult->Data as $currGroup)
        {
            //$objCreationResult = (new MobinitiGroupsModule())->CreateNew($currGroup);
            if (strpos(strtolower($currGroup->join_message),"ezcard.com") !== false)
            {
                $arJoinMessage = explode("/", $currGroup->join_message);
                $arJoinMessage = array_reverse($arJoinMessage);
                $strCardKeyword = str_replace("?", "", $arJoinMessage[0]) . PHP_EOL;
                $strCardKeyword = preg_replace("/[^A-Za-z0-9 ]/", '', $strCardKeyword);

                $objCardResult = (new Cards())->getWhere(["card_num" => $strCardKeyword]);

                if ($objCardResult->Result->Count === 0)
                {
                    $objCardResult = (new Cards())->getWhere(["card_keyword" => $strCardKeyword]);
                }

                if ($objCardResult->Result->Success === true)
                {
                    echo "CARD ID: " . $objCardResult->Data->First()->card_id . PHP_EOL;
                    echo "> Keyword: " . $strCardKeyword . PHP_EOL;
                    echo "> QUERY: " . $objCardResult->Result->Query . PHP_EOL;

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

            $strGroupNameId = explode(" ",$currGroup->name)[0];

            if (isInteger($strGroupNameId))
            {
                $objCardResult = (new Cards())->getWhere(["card_num" => $strGroupNameId]);

                if ($objCardResult->Result->Success === true)
                {
                    $currGroup->card_id = $objCardResult->Data->First()->card_id;
                    $objGroupUpdateResult = (new MobinitiGroups())->update($currGroup);
                }
            }
        }
    }
}