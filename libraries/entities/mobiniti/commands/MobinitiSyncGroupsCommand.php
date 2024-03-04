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
    public string $name = "Mobiniti.SyncGroups";
    public string $description = "Syncs all groups from available mobiniti groups.";

    /**
     * Executes the command
     */
    public function Run(): void
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

                $this->dump("Batch Start [{$intPageOffset}] Count = " . $objMobinitiApiResult->getData()->Count());

                if (($objMobinitiApiResult->result->Count + $objMobinitiApiResult->result->Depth) < $objMobinitiApiResult->result->Total)
                {
                    $intPageOffset++;
                }
                else
                {
                    $blnCylce = false;
                }

                $intCount = 0;

                $objMobinitiApiResult->getData()->Each(function(MobinitiGroupModel $currGroup, $currIndex) use ($objMobinitiGroup, &$intCount)
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
        $objMobinitiGroupModel = $objMobinitiGroup->getData()->FindEntityByValue("id", $currGroup->id);

        /** @var MobinitiGroupModel $objMobinitiGroupModel */
        if ($objMobinitiGroupModel !== null)
        {
            $objMobinitiGroupModel->name = $currGroup->name;

            if (!empty($currGroup->keyword->name))
            {
                $objMobinitiGroupModel->keyword = $currGroup->keyword->name;
            }

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

            if ($objMobinitiGroupResult->result->Success === true && $objMobinitiGroupResult->result->Count >= 1)
            {
                $this->dump("> Updating Mobiniti Group: [{$objMobinitiGroupModel->keyword}] " . $objMobinitiGroupResult->getData()->first()->id);
            }
            else
            {
                $this->dump("> Updating Mobiniti Group: [ERROR] " . $objMobinitiGroupResult->result->Message);
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
            $this->dump("> Adding Mobiniti Group: [{$objMobinitiGroupModel->keyword}] " . $objMobinitiGroupResult->getData()->first()->id);
        }
        else
        {
            $this->dump("> Adding Mobiniti Group: [ERROR] " . $objMobinitiGroupResult->result->Message);
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

                if ($objCardResult->result->Success === true && $objCardResult->result->Count >= 1)
                {
                    $this->dump("Linking [{$currGroup->id}] via Keyword To Card Num: " . $objCardResult->getData()->first()->card_id);

                    $objMobinitiGroupModel->card_id = $objCardResult->getData()->first()->card_id;
                    $objGroupUpdateResult = (new MobinitiGroups())->update($objMobinitiGroupModel);

                    if($objGroupUpdateResult->result->Success !== true)
                    {
                        echo $objGroupUpdateResult->result->Message . PHP_EOL;
                        echo $objGroupUpdateResult->result->Query . PHP_EOL;
                        print_r($objGroupUpdateResult->result->Errors); echo PHP_EOL;
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

                if ($objCardResult->result->Count === 0)
                {
                    $objCardResult = (new Cards)->getWhere(["card_keyword" => $strCardKeyword]);
                }

                if ($objCardResult->result->Success === true&& $objCardResult->result->Count >= 1)
                {
                    $this->dump("Linking [{$currGroup->id}] To Card Num: " . $objCardResult->getData()->first()->card_id);

                    $objMobinitiGroupModel->card_id = $objCardResult->getData()->first()->card_id;
                    $objGroupUpdateResult = (new MobinitiGroups())->update($objMobinitiGroupModel);

                    if($objGroupUpdateResult->result->Success !== true)
                    {
                        echo $objGroupUpdateResult->result->Message . PHP_EOL;
                        echo $objGroupUpdateResult->result->Query . PHP_EOL;
                        print_r($objGroupUpdateResult->result->Errors); echo PHP_EOL;
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

                    if ($objCardResult->result->Success === true && $objCardResult->result->Count >= 1)
                    {
                        $this->dump("Linking [{$currGroup->id}] To Card Num: " . $objCardResult->getData()->first()->card_id);

                        $objMobinitiGroupModel->card_id = $objCardResult->getData()->first()->card_id;
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
