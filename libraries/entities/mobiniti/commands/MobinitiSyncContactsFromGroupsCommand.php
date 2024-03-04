<?php

namespace Entities\Mobiniti\Commands;

use App\Utilities\Command\Command;
use Entities\Cards\Classes\Cards;
use Entities\Cards\Models\CardModel;
use Entities\Mobiniti\Classes\MobinitiContactGroupRels;
use Entities\Mobiniti\Classes\MobinitiContacts;
use Entities\Mobiniti\Classes\MobinitiContactUserRels;
use Entities\Mobiniti\Classes\MobinitiGroups;
use Entities\Mobiniti\Models\MobinitiContactGroupRelModel;
use Entities\Mobiniti\Models\MobinitiContactModel;
use Entities\Mobiniti\Models\MobinitiContactUserRelModel;
use Entities\Mobiniti\Models\MobinitiGroupModel;
use Vendors\Mobiniti\Main\V100\Classes\MobinitiContactsApiModule;

class MobinitiSyncContactsFromGroupsCommand extends Command
{
    public string $name = "Mobiniti.SyncContactsFromGroups";
    public string $description = "Syncs all contacts from available mobiniti groups.";

    /**
     * Executes the command
     */
    public function Run(): void
    {
        $objMobinitiContactGroupRelsModule = new MobinitiContactGroupRels();
        (new MobinitiGroups())->getAll(10000)->getData()->Each(function($currGroup) use ($objMobinitiContactGroupRelsModule)
        {
            $this->processMobinitiGroup($currGroup, $objMobinitiContactGroupRelsModule);

        });
    }

    private function processMobinitiGroup(MobinitiGroupModel $objGroup, MobinitiContactGroupRels $objMobinitiContactGroupRelsModule)
    {
        $objContactResult = (new MobinitiContactsApiModule())->GetContactsByGroupId($objGroup->id);

        $this->dump("Contacts for Group: " . $objContactResult->result->Count . " ID: " . $objGroup->id);

        $objContactResult->getData()->Each(function($currContact) use ($objGroup, $objMobinitiContactGroupRelsModule)
        {
            $objMobinitiContactsModule = new MobinitiContacts();
            $this->processMobinitiContact($currContact, $objGroup, $objMobinitiContactGroupRelsModule, $objMobinitiContactsModule);

        });

        $this->caller->updateCommandInsance(true, time());
    }

    private function processMobinitiContact($currContact, MobinitiGroupModel $objGroup, MobinitiContactGroupRels $objMobinitiContactGroupRelsModule, MobinitiContacts $objMobinitiContactsModule)
    {
        $objContactResult = $objMobinitiContactsModule->getWhere(["id" => $currContact->id]);

        if ($objContactResult->result->Count >= 1)
        {
            $this->processExistingContact($objContactResult->getData()->first(), $objGroup, $objMobinitiContactGroupRelsModule);
        }
        else
        {
            $this->processNewContact($currContact, $objGroup, $objMobinitiContactsModule, $objMobinitiContactGroupRelsModule);
        }

        $this->caller->updateCommandInsance(true, time());
        $this->dump("loop");
    }

    private function processExistingContact(MobinitiContactModel $objContact, MobinitiGroupModel $objGroup, MobinitiContactGroupRels $objMobinitiContactGroupRelsModule)
    {
        $objContactCardRelResult = $objMobinitiContactGroupRelsModule->getWhere(["mobiniti_contact_id" => $objContact->id, "mobiniti_group_id" => $objGroup->id]);
        $objContactCardRel = new MobinitiContactGroupRelModel();

        if ($objContactCardRelResult->result->Count > 0)
        {
            $objContactCardRel = $objContactCardRelResult->getData()->first();
            $objContactCardRel->card_id = $objGroup->card_id;

            $objContactCardRelCreationResult = $objMobinitiContactGroupRelsModule->update($objContactCardRel);

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
            $objContactCardRel->mobiniti_group_id = $objGroup->id;
            $objContactCardRel->card_id = $objGroup->card_id;
            $objContactCardRel->created_on = date("Y-m-d H:i:s", strtotime("now"));

            $objContactCardRelCreationResult = $objMobinitiContactGroupRelsModule->createNew($objContactCardRel);

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

        $this->caller->updateCommandInsance(true, time());
    }

    private function processNewContact($currContact, MobinitiGroupModel $objGroup, MobinitiContacts $objMobinitiContactsModule, MobinitiContactGroupRels $objMobinitiContactGroupRelsModule)
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

        $objContactCreationResult = $objMobinitiContactsModule->createNew($objContactModel);
        $objContact = $objContactCreationResult->getData()->first();

        if ($objContactCreationResult->result->Success === true)
        {
            $this->dump(">> Creating Contact: " . $objContact->id);
        }
        else
        {
            $this->dump(">> Creating Contact: [ERROR] " . $objContactCreationResult->result->Message);
        }

        $this->caller->updateCommandInsance(true, time());

        $this->processContactConnectionToGroup($objContact, $objGroup, $objMobinitiContactGroupRelsModule);
    }

    private function processContactConnectionToGroup($objContact, $objGroup, MobinitiContactGroupRels $objMobinitiContactGroupRelsModule)
    {
        $objContactCardRelResult = $objMobinitiContactGroupRelsModule->getWhere(["mobiniti_contact_id" => $objContact->id, "mobiniti_group_id" => $objGroup->id]);

        if ($objContactCardRelResult->result->Count > 0)
        {
            $this->processExistingContactRel($objContactCardRelResult->getData()->first(), $objGroup, $objMobinitiContactGroupRelsModule);
        }
        else
        {
            $this->processNewContactRel($objContact, $objGroup, $objMobinitiContactGroupRelsModule);
        }

        $objMobinitiContactUserRelsModule = new MobinitiContactUserRels();

        $this->associateContactToUserIfApplicable($objContact, $objGroup, $objMobinitiContactUserRelsModule);
    }

    private function processExistingContactRel($objContactCardRel, MobinitiGroupModel $objGroup, MobinitiContactGroupRels $objMobinitiContactGroupRelsModule)
    {
        $objContactCardRel->card_id = $objGroup->card_id;

        $objContactCardRelCreationResult = $objMobinitiContactGroupRelsModule->update($objContactCardRel);

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

    private function processNewContactRel($objContact, $objGroup, MobinitiContactGroupRels  $objMobinitiContactGroupRelsModule)
    {
        $objContactCardRel = new MobinitiContactGroupRelModel();
        $objContactCardRel->mobiniti_contact_id = $objContact->id;
        $objContactCardRel->mobiniti_group_id = $objGroup->id;
        $objContactCardRel->created_on = date("Y-m-d H:i:s", strtotime("now"));

        $objContactCardRelCreationResult = $objMobinitiContactGroupRelsModule->createNew($objContactCardRel);

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

    private function associateContactToUserIfApplicable($objContact, $objGroup, MobinitiContactUserRels $objMobinitiContactUserRelsModule)
    {
        $objCardResult = (new Cards())->getById($objGroup->card_id);

        if ($objCardResult->result->Count === 0)
        {
            return;
        }

        $objCard = $objCardResult->getData()->first();

        $objContactUserRelResult = $objMobinitiContactUserRelsModule->getWhere(["mobiniti_contact_id" => $objContact->id, "user_id" => $objCard->owner_id]);

        if ($objContactUserRelResult->result->Count > 0)
        {
            return;
        }

        $objContactUserRel = new MobinitiContactUserRelModel();
        $this->assignMobinitiContactToUser($objContact, $objCard, $objContactUserRel, $objMobinitiContactUserRelsModule);
    }

    private function assignMobinitiContactToUser($objContact, CardModel $objCard, $objContactUserRel, MobinitiContactUserRels $objMobinitiContactUserRelsModule)
    {
        $objContactUserRel->user_id = $objCard->owner_id;
        $objContactUserRel->mobiniti_contact_id = $objContact->id;

        $objContactUserRelCreationResult = $objMobinitiContactUserRelsModule->createNew($objContactUserRel);

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

    protected function dump($string)
    {
        dump($string);
    }
}
