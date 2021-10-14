<?php

namespace Entities\Mobiniti\Commands;

use App\Utilities\Command\Command;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Cards\Classes\Cards;
use Entities\Mobiniti\Classes\MobinitiGroups;
use Entities\Mobiniti\Models\MobinitiGroupModel;
use Vendors\Mobiniti\Main\V100\Classes\MobinitiGroupsApiModule;

class MobinitiSyncGroupsCommand extends Command
{
    public $name = "Mobiniti.SyncGroups";
    public $description = "Syncs all groups from available mobiniti groups.";

    /**
     * Executes the command
     */
    public function Run()
    {
        $this->dump($this->name . " [START]");

        $blnCylce = true;
        $intPageOffset = 1;
        $objMobinitiGroup = (new MobinitiGroups())->getAll();

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

                $objMobinitiApiResult->Data->Each(function(MobinitiGroupModel $currGroup, $currIndex) use ($objMobinitiGroup, &$intCount)
                {
                    $this->syncMobinitiGroup($currGroup, $objMobinitiGroup);
                    $intCount++;
                });

                $this->dump("Total Processed: " . $intCount);
                $this->caller->updateCommandInsance(true, time());
            }
        }
        catch(\Exception $ex)
        {
            $this->dump($ex->getMessage());
            $this->dump($ex);
        }

    }

    protected function syncMobinitiGroup(MobinitiGroupModel $currGroup, ExcellTransaction $objMobinitiGroup)
    {
        $objMobinitiGroupModel = $objMobinitiGroup->Data->FindEntityByValue("id", $currGroup->id);

        /** @var MobinitiGroupModel $objMobinitiGroupModel */
        if ($objMobinitiGroupModel !== null)
        {
            $objMobinitiGroupModel->name = $currGroup->name;

            if (!empty($currGroup->keyword->name))
            {
                $objMobinitiGroupModel->keyword = $currGroup->keyword->name;
            }

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

            if ($objMobinitiGroupResult->Result->Success === true && $objMobinitiGroupResult->Result->Count >= 1)
            {
                $this->dump("> Updating Mobiniti Group: [{$objMobinitiGroupModel->keyword}] " . $objMobinitiGroupResult->Data->First()->id);
            }
            else
            {
                $this->dump("> Updating Mobiniti Group: [ERROR] " . $objMobinitiGroupResult->Result->Message);
            }

            $this->syncMobinitiGroupWithCards($currGroup, $objMobinitiGroupModel);

            $this->caller->updateCommandInsance(true, time());

            return;
        }

        $objMobinitiGroupModel = new MobinitiGroupModel();
        $objMobinitiGroupModel->id = $currGroup->id;
        $objMobinitiGroupModel->name = $currGroup->name;

        if (!empty($currGroup->keyword->name))
        {
            $objMobinitiGroupModel->keyword = $currGroup->keyword->name;
        }

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
            $this->dump("> Adding Mobiniti Group: [{$objMobinitiGroupModel->keyword}] " . $objMobinitiGroupResult->Data->First()->id);
        }
        else
        {
            $this->dump("> Adding Mobiniti Group: [ERROR] " . $objMobinitiGroupResult->Result->Message);
        }

        $this->caller->updateCommandInsance(true, time());

        $this->syncMobinitiGroupWithCards($currGroup, $objMobinitiGroupModel);

        $this->caller->updateCommandInsance(true, time());
    }

    protected function syncMobinitiGroupWithCards($currGroup, $objMobinitiGroupModel)
    {
        try
        {
            if (!empty($currGroup->keyword->name))
            {
                $strCardKeyword = $currGroup->keyword->name;

                $objCardResult = (new Cards())->getWhere(["card_keyword" => $strCardKeyword]);

                if ($objCardResult->Result->Success === true && $objCardResult->Result->Count >= 1)
                {
                    $this->dump("Linking [{$currGroup->id}] via Keyword To Card Num: " . $objCardResult->Data->First()->card_id);

                    $objMobinitiGroupModel->card_id = $objCardResult->Data->First()->card_id;
                    $objGroupUpdateResult = (new MobinitiGroups())->update($objMobinitiGroupModel);

                    if($objGroupUpdateResult->Result->Success !== true)
                    {
                        echo $objGroupUpdateResult->Result->Message . PHP_EOL;
                        echo $objGroupUpdateResult->Result->Query . PHP_EOL;
                        print_r($objGroupUpdateResult->Result->Errors); echo PHP_EOL;
                    }

                    $this->caller->updateCommandInsance(true, time());
                }
            }

            if (strpos(strtolower($currGroup->join_message),"ezcard.com") !== false)
            {
                $arJoinMessage = explode("/", $currGroup->join_message);
                $arJoinMessage = array_reverse($arJoinMessage);
                $strCardKeyword = str_replace("?", "", $arJoinMessage[0]);
                $strCardKeyword = preg_replace("/[^A-Za-z0-9 ]/", '', $strCardKeyword);

                $objCardResult = (new Cards)->getWhere(["card_num" => $strCardKeyword]);

                if ($objCardResult->Result->Count === 0)
                {
                    $objCardResult = (new Cards)->getWhere(["card_keyword" => $strCardKeyword]);
                }

                if ($objCardResult->Result->Success === true&& $objCardResult->Result->Count >= 1)
                {
                    $this->dump("Linking [{$currGroup->id}] To Card Num: " . $objCardResult->Data->First()->card_id);

                    $objMobinitiGroupModel->card_id = $objCardResult->Data->First()->card_id;
                    $objGroupUpdateResult = (new MobinitiGroups())->update($objMobinitiGroupModel);

                    if($objGroupUpdateResult->Result->Success !== true)
                    {
                        echo $objGroupUpdateResult->Result->Message . PHP_EOL;
                        echo $objGroupUpdateResult->Result->Query . PHP_EOL;
                        print_r($objGroupUpdateResult->Result->Errors); echo PHP_EOL;
                    }

                    $this->caller->updateCommandInsance(true, time());
                }
            }
            else
            {
                $strGroupNameId = explode(" ",$currGroup->name)[0];

                if (isInteger($strGroupNameId))
                {
                    $objCardResult = (new Cards)->getWhere(["card_num" => $strGroupNameId]);

                    if ($objCardResult->Result->Success === true && $objCardResult->Result->Count >= 1)
                    {
                        $this->dump("Linking [{$currGroup->id}] To Card Num: " . $objCardResult->Data->First()->card_id);

                        $objMobinitiGroupModel->card_id = $objCardResult->Data->First()->card_id;
                        $objGroupUpdateResult = (new MobinitiGroups())->update($objMobinitiGroupModel);
                    }

                    $this->caller->updateCommandInsance(true, time());
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
