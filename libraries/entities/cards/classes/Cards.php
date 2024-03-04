<?php

namespace Entities\Cards\Classes;

use App\Core\AppEntity;
use App\Utilities\Database;
use App\Utilities\Excell\ExcellCollection;
use App\Utilities\Excell\ExcellRelationship;
use App\Utilities\Transaction\ExcellTransaction;
use ConnectionsModule;
use Entities\Cards\Models\CardModel;
use Entities\Cards\Models\CardPageModel;
use Entities\Cards\Models\CardPageRelModel;
use Entities\Media\Classes\Images;
use Entities\Media\Models\ImageModel;
use Entities\Users\Classes\ConnectionRels;
use Entities\Users\Classes\Connections;
use Entities\Users\Models\ConnectionModel;
use Entities\Users\Models\ConnectionRelModel;
use Entities\Orders\Models\OrderLineModel;

class Cards extends AppEntity
{
    public string $strEntityName       = "Cards";
    public $strDatabaseTable    = "card";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = CardModel::class;
    public $strMainModelPrimary = "card_id";
    public $isPrimaryModule     = true;

    public function main_imageRelationship() : ExcellRelationship
    {
        return $this->buildRelationshipModel("main_image", "Media", "image", "url", "entity_id", "card_id", ["entity_name" => "card", "image_class" => "main-image"]);
    }

    public function main_thumbRelationship() : ExcellRelationship
    {
        return $this->buildRelationshipModel("main_thumb", "Media", "image", "thumb", "entity_id", "card_id", ["entity_name" => "card", "image_class" => "main-image"]);
    }

    public function card_ownerRelationship() : ExcellRelationship
    {
        return $this->buildRelationshipModel("card_owner_name", "Main", "user", ["first_name", "last_name"], "user_id", "owner_id");
    }

    public function card_contactsRelationship() : ExcellRelationship
    {
        return $this->buildRelationshipModel("card_contacts", "Main", "contact_card_rel", "COUNT()", "card_id", "card_id");
    }

    public function card_mobiniti_contactsRelationship() : ExcellRelationship
    {
        return $this->buildRelationshipModel("card_contacts", "Main", "contact_user_rel", "COUNT()", "card_id", "card_id");
    }

    public function GetByCardNum($intCardNum, $companyFilter = false) : ExcellTransaction
    {
        if (!(new CardModel())->Add("card_num", $intCardNum))
        {
            return new ExcellTransaction(false, "The value passed in for CardNum did not pass validation: " . $intCardNum);
        }

        $whereClause = ["card_num" => $intCardNum];

        if ($companyFilter === true)
        {
            $whereClause["company_id"] = $this->companyId;
        }

        return $this->getWhere($whereClause, 1);
    }

    public function GetByCardVanityUrl($vanityUrl, $companyIdFilter = null) : ExcellTransaction
    {
        $objEntityModel = new CardModel();

        if (!$objEntityModel->Add("card_vanity_url", $vanityUrl))
        {
            return new ExcellTransaction(false, "The value passed in for Vanity Url did not pass validation: " . $vanityUrl);
        }

        $whereClause = ["card_vanity_url" => $vanityUrl];

        if ($companyIdFilter !== null)
        {
            $whereClause["company_id"] = $companyIdFilter;
        }

        return $this->getWhere($whereClause, 1);
    }

    public function GetByUserId(int $intUserId, $scoped = true) : ExcellTransaction
    {
        $objCardResult = new ExcellTransaction();
        $objCardsAsOtherRelsResult = new ExcellTransaction();

        $blnReplaceFks = $this->blnFksReplace;

        if ( empty($intUserId) || !isInteger($intUserId))
        {
            $objCardResult->result->Success = false;
            $objCardResult->result->Count = 0;
            $objCardResult->result->Message = "You must supply a valid user id.";
            return $objCardResult;
        }

        $whereClause = ["owner_id" => $intUserId];

        if ($scoped === true)
        {
            $whereClause = ["owner_id" => $intUserId, "company_id" => $this->app->objCustomPlatform->getCompanyId()];
        }

        $objCardsAsOwnerResult = $this->getWhere($whereClause);

        if ( $objCardsAsOwnerResult->result->Success === true && $objCardsAsOwnerResult->result->Count > 0)
        {
            foreach($objCardsAsOwnerResult->data as $intCardId => $objCardData)
            {

                $objCardsAsOwnerResult->getData()->{$intCardId}->AddUnvalidatedValue("user_role", "Card Owner");
                $objCardsAsOwnerResult->getData()->{$intCardId}->AddUnvalidatedValue("user_rel_type_id", 1);
            }
        }

        $whereClause = ["user_id" => $intUserId];

        if ($scoped === true)
        {
            $whereClause = ["user_id" => $intUserId, "company_id" => $this->app->objCustomPlatform->getCompanyId()];
        }

        $objCardRelsModule = new CardRels();
        $objCardRelResult = $objCardRelsModule->noFks()->getWhere($whereClause);

        if ( $objCardRelResult->result->Success === true && $objCardRelResult->result->Count > 0)
        {
            $arCardUserIds = $objCardRelResult->getData()->FieldsToArray(["card_id"]);

            $objCardsAsOtherRelsResult = $this->getWhereIn("card_id", $arCardUserIds);
            $objCardsAsOtherRelsResult->getData()->MergeFields($objCardRelResult->data,["card_rel_type_id" => "user_rel_type_id","status" => "user_rel_status"],["card_id"]);
            $objCardsAsOtherRelsResult->getData()->MergeFields($objCardRelResult->data,["name" => "user_rel_role","card_rel_permissions" => "user_rel_permissions"],["card_rel_type_id"]);
        }

        if ($objCardsAsOwnerResult->result->Count > 0 && $objCardsAsOtherRelsResult->result->Count === 0)
        {
            return $objCardsAsOwnerResult;
        }

        if ($objCardsAsOwnerResult->result->Count === 0 && $objCardsAsOtherRelsResult->result->Count > 0)
        {
            return $objCardsAsOtherRelsResult;
        }

        $objCardsAsOwnerResult->getData()->Merge($objCardsAsOtherRelsResult->data);
        return $objCardsAsOwnerResult;
    }

    public function getByUuid($uuid) : ExcellTransaction
    {

        $objWhereClause = "
            SELECT card.*,
            (SELECT platform_name FROM `excell_main`.`company` WHERE company.company_id = card.company_id LIMIT 1) AS platform, 
            (SELECT url FROM `excell_media`.`image` WHERE image.entity_id = card.card_id AND image.entity_name = 'card' AND image_class = 'main-image' ORDER BY image_id DESC LIMIT 1) AS banner, 
            (SELECT url FROM `excell_media`.`image` WHERE image.entity_id = card.card_id AND image.entity_name = 'card' AND image_class = 'favicon-image' ORDER BY image_id DESC LIMIT 1) AS favicon,
            (SELECT url FROM `excell_media`.`image` WHERE image.entity_id = card.card_id AND image.entity_name = 'card' AND image_class = 'user-avatar-image' ORDER BY image_id DESC LIMIT 1) AS user_avatar,
            (SELECT url FROM `excell_media`.`image` WHERE image.entity_id = card.card_id AND image.entity_name = 'card' AND image_class = 'logo-image' ORDER BY image_id DESC LIMIT 1) AS logo,
            (SELECT url FROM `excell_media`.`image` WHERE image.entity_id = card.card_id AND image.entity_name = 'card' AND image_class = 'splash-cover-image' ORDER BY image_id DESC LIMIT 1) AS splash_cover,
            (SELECT thumb FROM `excell_media`.`image` WHERE image.entity_id = card.card_id AND image.entity_name = 'card' AND image_class = 'favicon-image' ORDER BY image_id DESC LIMIT 1) AS ico,
            (SELECT CONCAT(user.first_name, ' ', user.last_name) FROM `excell_main`.`user` WHERE user.user_id = card.owner_id LIMIT 1) AS card_owner_name,
            (SELECT user.sys_row_id FROM `excell_main`.`user` WHERE user.user_id = card.owner_id LIMIT 1) AS card_owner_uuid,
            (SELECT user.user_id FROM `excell_main`.`user` WHERE user.user_id = card.owner_id LIMIT 1) AS card_owner_id,
            (SELECT CONCAT(user.first_name, ' ', user.last_name) FROM `excell_main`.`user` WHERE user.user_id = card.card_user_id LIMIT 1) AS card_user_name,
            (SELECT user.sys_row_id FROM `excell_main`.`user` WHERE user.user_id = card.card_user_id LIMIT 1) AS card_user_uuid,
            (SELECT user.status FROM `excell_main`.`user` WHERE user.user_id = card.card_user_id LIMIT 1) AS card_user_status,
            (SELECT cn.connection_value FROM `excell_main`.`user` ur LEFT JOIN `excell_main`.`connection` cn ON ur.user_email = cn.connection_id WHERE ur.user_id = card.card_user_id LIMIT 1) AS card_user_email,
            (SELECT cn.connection_value FROM `excell_main`.`user` ur LEFT JOIN `excell_main`.`connection` cn ON ur.user_phone = cn.connection_id WHERE ur.user_id = card.card_user_id LIMIT 1) AS card_user_phone,
            (SELECT title FROM `excell_main`.`product` WHERE product.product_id = card.product_id LIMIT 1) AS product, 
            (SELECT name FROM `excell_main`.`card_type` WHERE card_type.card_type_id = card.card_type_id LIMIT 1) AS card_type_label, 
            (SELECT name FROM `excell_main`.`card_template` WHERE card_template.card_template_id = card.template_id LIMIT 1) AS template_name, 
            (SELECT COUNT(*) FROM `excell_main`.`contact_card_rel` mcgr WHERE mcgr.card_id = card.card_id) AS card_contacts
            FROM `card` ";

        $objWhereClause .= "WHERE sys_row_id = '".$uuid."'";

        $objWhereClause .= " LIMIT 1";

        $cardResult = Database::getSimple($objWhereClause, "card_num");
        $cardResult->getData()->HydrateModelData(CardModel::class, true);

        if ($cardResult->getResult()->Count !== 1)
        {
            return new ExcellTransaction(false, $cardResult->getResult()->Message, null, 0, [$cardResult->result->Message]);
        }

        if (!empty($cardResult->getData()->first()->card_data) && isJson($cardResult->getData()->first()->card_data)) { $cardResult->getData()->first()->card_data = json_decode($cardResult->getData()->first()->card_data); }

        return $cardResult;
    }

    public function GetCardByGroupId($intGroupId) : ExcellTransaction
    {
        $objCardResult = new ExcellTransaction();

        if ( empty($intGroupId) || !isInteger($intGroupId))
        {
            $objCardResult->result->Success = false;
            $objCardResult->result->Count = 0;
            $objCardResult->result->Message = "You must supply a valid group id.";
            return $objCardResult;
        }

        $strCardRelQuery = "SELECT * FROM card_rel_group "
            . "WHERE card_rel_group_id = '$intGroupId' AND status = 'Active'";

        $objCardRel = Database::getSimple($strCardRelQuery,"card_rel_group_id");

        $objCardWhereclause = array();

        foreach($objCardRel->data as $currCardRelId => $objCardRel)
        {
                $objCardWhereclause[] = ["card_id", "=", $objCardRel->card_id];
            $objCardWhereclause[] = ["OR"];
        }

        array_pop($objCardWhereclause);

        $objCards = $this->getWhere($objCardWhereclause);

        return $objCards;
    }

    public function GetAllCardsForDisplay($intCount, $offset = 0): ExcellTransaction
    {
        if ($intCount !== "all")
        {
            $objCards = $this->getWhere(["company_id" => $this->app->objCustomPlatform->getCompany()->company_id], "card_id.DESC", [$offset, $intCount]);
            return $objCards;
        }

        $objCards = $this->getWhere(["company_id" => $this->app->objCustomPlatform->getCompany()->company_id],"card_id");
        return $objCards;
    }

    public function GetAllCardTypes(): ExcellTransaction
    {
        $strCardTypeQuery = "SELECT * FROM card_type;";

        $objCardType = Database::getSimple($strCardTypeQuery,"card_type_id");

        return $objCardType;
    }

    public function GetConnectionsByCardId($intCardId): ExcellTransaction
    {
        $objConnectionResult = new ExcellTransaction();

        if ( empty($intCardId) || !isInteger($intCardId))
        {
            $objConnectionResult->result->Success = false;
            $objConnectionResult->result->Count = 0;
            $objConnectionResult->result->Message = "You must supply a valid card id.";
            return $objConnectionResult;
        }

        $strCardConnectionQuery = "SELECT * FROM connection_rel " .
            "LEFT JOIN connection ON connection.connection_id = connection_rel.connection_id " .
            "WHERE connection_rel.card_id = $intCardId ORDER BY connection_rel.display_order ASC";

        if ($this->blnFksReplace === true)
        {
            $strCardConnectionQuery = "SELECT " .
                "connection_rel_id, " .
                "connection_rel.connection_id, " .
                "connection_rel.display_order, " .
                "company_id, " .
                "division_id, " .
                "card_id, " .
                "user_id, " .
                "(SELECT name FROM connection_type WHERE connection_type.connection_type_id = connection.connection_type_id) AS connection_type_id, " .
                "connection.connection_value, " .
                "action, " .
                "display_order, " .
                "is_primary, " .
                "status, " .
                "connection_class " .
                "FROM connection_rel " .
                "LEFT JOIN connection ON connection.connection_id = connection_rel.connection_id " .
                "WHERE connection_rel.card_id = $intCardId ORDER BY connection_rel.display_order ASC";
        }

        $objCardConnections = Database::getSimple($strCardConnectionQuery,"display_order");

        return $objCardConnections;
    }

    public function GetCardRelTypes($arFilter = null): ExcellTransaction
    {
        $strCardRelTypeQuery = "SELECT * FROM card_rel_type";

        if(!empty($arFilter["NOT"]) && is_array($arFilter["NOT"]))
        {
            $strCardRelTypeQuery .= " WHERE ";
            $arFilterClause = [];

            foreach($arFilter["NOT"] as $strNotIn)
            {
                $arFilterClause[] = "card_rel_type_id != " . $strNotIn;
            }

            $strCardRelTypeQuery .= implode(" && ", $arFilterClause);
        }

        $strCardRelTypeQuery .= ";";

        $objCardRelTypeResult = Database::getSimple($strCardRelTypeQuery,"card_rel_type_id");

        return $objCardRelTypeResult;
    }

    public function GetCardOwner($intCardId) : ?ExcellTransaction
    {
        $objCardOwnerResult = new ExcellTransaction();

        if ( empty($intCardId) || !isInteger($intCardId))
        {
            $objCardOwnerResult->result->Success = false;
            $objCardOwnerResult->result->Count = 0;
            $objCardOwnerResult->result->Message = "You must supply a valid card id.";
            return $objCardOwnerResult;
        }

        $objCardRelsModule = new CardRels();
        $colUsersResult = $objCardRelsModule->GetUsersByCardId($intCardId);

        if ($colUsersResult->result->Count === 0)
        {
            return $colUsersResult;
        }

        $objCardOwnerResult->result->Success = true;
        $objCardOwnerResult->result->Count = 1;
        $objCardOwnerResult->getData()->{0} = $colUsersResult->getData()->FindEntityByValue("card_rel_type_id",1);

        return $objCardOwnerResult;
    }

    public function GetCardsByAffiliateId($intUserId) : ExcellTransaction
    {
        $objCardsResult = new ExcellTransaction();

        if ( empty($intUserId) || !isInteger($intUserId))
        {
            $objCardsResult->result->Success = false;
            $objCardsResult->result->Count = 0;
            $objCardsResult->result->Message = "You must supply a valid user id.";
            return $objCardsResult;
        }

        $blnReEnableFks = false;
        $this->disableFksTemporarily($blnReEnableFks);

        $objCardRelsModule = new CardRels();
        $objCardRelResult = $objCardRelsModule->getWhere(["user_id" => $intUserId, "card_rel_type_id" => 9]);

        if ($objCardRelResult->result->Count === 0)
        {
            $objCardsResult->result->Success = false;
            $objCardsResult->result->Count = 0;
            $objCardsResult->result->Message = "0 Cards were found for this affiliate with id: " . $intUserId;

            $this->renableFksIfTemporarilyDisabled($blnReEnableFks);

            return $objCardsResult;
        }

        $arCardId = $objCardRelResult->getData()->FieldsToArray(["card_id"]);

        $objCardsResult = $this->getWhereIn("card_id", $arCardId);

        if ($objCardsResult->result->Count === 0)
        {
            $objCardsResult->result->Success = false;
            $objCardsResult->result->Count = 0;
            $objCardsResult->result->Message = "0 Cards were found for this affiliate with id: " . $intUserId;

            $this->blnFksReplace = true;

            return $objCardsResult;
        }

        $this->renableFksIfTemporarilyDisabled($blnReEnableFks);

        return $objCardsResult;
    }

    public function CloneCardSettings($sourceCardId, $destinationCardId) : ExcellTransaction
    {
        $objCards = new Cards();
        $objSourceCardResult = $objCards->getById($sourceCardId);
        $objDestinationCardResult = $objCards->getById($destinationCardId);

        if($objSourceCardResult->result->Count === 0)
        {
            return new ExcellTransaction(false, "Source card {$sourceCardId} was not found for cloning.");
        }

        if($objDestinationCardResult->result->Count === 0)
        {
            return new ExcellTransaction(false, "Destination card {$destinationCardId} was not found for cloning.");
        }

        $objDestinationCard = $objDestinationCardResult->getData()->first();
        $objSourceCard = $objSourceCardResult->getData()->first();

        $objDestinationCard->card_data = $objSourceCard->card_data;
        $objDestinationCard->template_id = $objSourceCard->template_id;

        return $objCards->update($objDestinationCard);
    }

    public function CloneCardPrimaryImage($sourceCardId, $destinationCardId) : ExcellTransaction
    {
        $objImages = new Images();
        $imageResult = $objImages->getWhere(["entity_id" => $sourceCardId, "entity_name" => "card", "image_class" => "main-image"],"image_id.DESC", 1);

        if ($imageResult->result->Count !== 1)
        {
            return $imageResult;
        }

        $image = $imageResult->getData()->first();
        $image->clearField("image_id");

        $newImage = new ImageModel($image);
        $newImage->entity_id =$destinationCardId;

        return $objImages->createNew($newImage);
    }

    public function CloneCardConnections($sourceCardId, $destinationCardId) : ExcellTransaction
    {
        $objCards = new Cards();
        $objSourceCardResult = $objCards->getById($sourceCardId);
        $objDestinationCardResult = $objCards->getById($destinationCardId);

        if($objSourceCardResult->result->Count === 0)
        {
            return new ExcellTransaction(false, "Source card {$sourceCardId} was not found for cloning.");
        }

        if($objDestinationCardResult->result->Count === 0)
        {
            return new ExcellTransaction(false, "Destination card {$destinationCardId} was not found for cloning.");
        }

        $objDestinationCard = $objDestinationCardResult->getData()->first();

        $deleteDestinationConnectionsResult = (new ConnectionRels())->deleteWhere(["card_id" => $destinationCardId]);

        if ($deleteDestinationConnectionsResult->result->Success === false)
        {
            return $deleteDestinationConnectionsResult;
        }

        $objConnectionRels = new ConnectionRels();
        $connectionRelResult = $objConnectionRels->getWhere(["card_id" => $sourceCardId]);

        if ($connectionRelResult->result->Count === 0)
        {
            return $connectionRelResult;
        }

        $objConnections = new Connections();
        $connectionResults = $objConnections->getWhereIn("connection_id", $connectionRelResult->getData()->FieldsToArray(["connection_id"]));

        if ($connectionResults->result->Count === 0)
        {
            return $connectionResults;
        }

        $connectionRelResult->getData()->HydrateChildModelData("connection",["connection_id" => "connection_id"],$connectionResults->data, true);

        $errors = new ExcellCollection();

        $connectionRelResult->getData()->Each(static function($currConnectionRel) use ($objConnectionRels, $objConnections, $objDestinationCard, &$errors) {

            $currConnectionRel->connection->clearField("connection_id");
            $connection = new ConnectionModel($currConnectionRel->connection);
            $connection->user_id = $objDestinationCard->owner_id;
            $newConnectionResult = $objConnections->createNew($connection);

            if ($newConnectionResult->result->Success === false)
            {
                $errors->Add($newConnectionResult->result);
                return;
            }

            $newConnection = $newConnectionResult->getData()->first();

            $currConnectionRel->clearField("connection_rel_id");
            $connectionRel = new ConnectionRelModel($currConnectionRel);
            $connectionRel->card_id = $objDestinationCard->card_id;
            $connectionRel->connection_id = $newConnection->connection_id;
            $newConnectionRelResult = $objConnectionRels->createNew($connectionRel);

            if ($newConnectionRelResult->result->Success === false)
            {
                $errors->Add($newConnectionRelResult->result);
            }
        });

        return new ExcellTransaction(true, "Completed cloning card connections.");
    }

    public function CloneCardPages($sourceCardId, $destinationCardId, $backup = false, $deletePages = true) : ExcellTransaction
    {
        $objCards = new Cards();
        $objSourceCardResult = $objCards->getById($sourceCardId);
        $objDestinationCardResult = $objCards->getById($destinationCardId);

        if($objSourceCardResult->result->Count === 0)
        {
            return new ExcellTransaction(false, "Source card {$sourceCardId} was not found for cloning.");
        }

        if($objDestinationCardResult->result->Count === 0)
        {
            return new ExcellTransaction(false, "Destination card {$destinationCardId} was not found for cloning.");
        }

        $objDestinationCard = $objDestinationCardResult->getData()->first();
        $objDestinationCard->LoadFullCard(false);

        if ($backup === true)
        {
            $cardPageArchives = new CardPageArchives();
            $cardPageArchives->backupExistingCardPagesFromCard($objDestinationCard);
        }

        if (!empty($objDestinationCard->Tabs) && $deletePages === true)
        {
            $arDestCardIds = $objDestinationCard->Tabs->FieldsToArray(["card_tab_id"]);

            // Delete NON Mirror/Library tabs
            $objDeleteDestinationCardPagesResult = (new CardPage())->deleteWhere([["card_tab_id", "IN", $arDestCardIds], "AND", ["library_tab" => "0", "permanent" => "0"]]);

            // Delete Tab Rels
            $objDeleteDestinationCardPageRelsResult = (new CardPageRels())->deleteWhere(["card_id" => $objDestinationCard->card_id]);
        }

        $objSourceCard = $objSourceCardResult->getData()->first();
        $objSourceCard->LoadFullCard(false);
        $arErrors = [];

        foreach($objSourceCard->Tabs as $currCardPage)
        {
            $this->CloneCardPage($currCardPage, $objDestinationCard, $arErrors);
        }

        return new ExcellTransaction(true, "Card successfully cloned.");
    }

    protected function CloneCardPage($currCardPage, $objDestinationCard, &$arErrors) : ExcellTransaction
    {
        $intCardPageId = $currCardPage->card_tab_id;

        if ($currCardPage->library_tab != true)
        {
            $objCardPage = new CardPageModel();
            $objCardPage->user_id = $objDestinationCard->owner_id;
            $objCardPage->company_id = $objDestinationCard->company_id;
            $objCardPage->division_id = $objDestinationCard->division_id;
            $objCardPage->card_tab_type_id = $currCardPage->card_tab_type_id;
            $objCardPage->title = $currCardPage->title;
            $objCardPage->content = $currCardPage->content;
            $objCardPage->library_tab = $currCardPage->library_tab;
            $objCardPage->permanent = $currCardPage->permanent;
            $objCardPage->order_number = $currCardPage->order_number;
            $objCardPage->visibility = $currCardPage->visibility ? EXCELL_TRUE : EXCELL_FALSE;
            $objCardPage->created_by = $this->app->objCustomPlatform->getCompany()->default_sponsor_id;
            $objCardPage->updated_by = $this->app->objCustomPlatform->getCompany()->default_sponsor_id;

            $objNewCardPageResult = (new CardPage())->getFks()->createNew($objCardPage);

            if ($objNewCardPageResult->result->Success === false)
            {
                return new ExcellTransaction(false, "Unable to save card page: " . $objNewCardPageResult->result->Message);
            }

            $intCardPageId = $objNewCardPageResult->getData()->first()->card_tab_id;
        }

        $objCardPageRelResult = new CardPageRelModel();
        $objCardPageRelResult->card_tab_id = $intCardPageId;
        $objCardPageRelResult->card_id = $objDestinationCard->card_id;
        $objCardPageRelResult->user_id = $objDestinationCard->owner_id;
        $objCardPageRelResult->rel_sort_order = $currCardPage->rel_sort_order;
        $objCardPageRelResult->rel_visibility = $currCardPage->rel_visibility ? EXCELL_TRUE : EXCELL_FALSE;
        $objCardPageRelResult->card_tab_rel_type = $currCardPage->card_tab_rel_type;
        $objCardPageRelResult->card_tab_rel_data = $currCardPage->card_tab_rel_data;
        $objCardPageRelResult->synced_state = $currCardPage->synced_state;

        $objNewCardPageRelResult = (new CardPageRels())->getFks()->createNew($objCardPageRelResult);

        if ($objNewCardPageRelResult->result->Success === false)
        {
            return new ExcellTransaction(false, "Unable to save card page rel: " . $objNewCardPageRelResult->result->Message);
        }

        return new ExcellTransaction(true, "Card page ({$intCardPageId}) successfully cloned.");
    }

    public function DeleteInvalidTemplatePages($defaultTemplateId, $cardId, $packageLine) : void
    {
        global $app;

        if ($packageLine->package_id === 10 && $app->objCustomPlatform->getCompanyId() === 4)
        {
            $objPagesForDeletionResult = (new CardPageRels())->getwhere(["synced_state" => 1, "card_id" => $cardId]);

            if ($objPagesForDeletionResult->result->Count === 0) { return; }

            $arDestCardIds = $objPagesForDeletionResult->getData()->FieldsToArray(["card_tab_id"]);

            // Delete Pages
            (new CardPage())->deleteWhere([["card_tab_id", "IN", $arDestCardIds]], 1);

            // Delete Page Rels
            (new CardPageRels())->deleteWhere(["synced_state" => 1, "card_id" => $cardId], 1);
        }
    }

    public function update($objCard, $blnReplaceCarots = false) : ExcellTransaction
    {
        $objUpdatedCardResult = parent::update($objCard);

        if ( $objUpdatedCardResult->result->Success === false)
        {
            return $objUpdatedCardResult;
        }

        $objUpdatedCard = $objUpdatedCardResult->getData()->first();

        if (!empty($objCard->Tabs) && is_a($objCard->Tabs, \App\Utilities\Excell\ExcellCollection::class))
        {
            $objCardPagesModule = new CardPage();
            foreach($objCard->Tabs as $intCardIndex => $objCardPages)
            {
                $objUpdatedCardPagesResult = $objCardPagesModule->update($objCardPages, $blnReplaceCarots);

                if ( $objUpdatedCardPagesResult->result->Success === false)
                {
                    return $objUpdatedCardPagesResult;
                }
            }
        }

        if (!empty($objCard->Connections) && is_a($objCard->Connections, \App\Utilities\Excell\ExcellCollection::class))
        {
            foreach($objCard->Connections as $intConnectionIndex => $objCardConnection)
            {
                $objUpdatedCardConnectionsResult = CardConnections::update($objCardConnection);

                if ( $objUpdatedCardConnectionsResult->result->Success === false)
                {
                    return $objUpdatedCardConnectionsResult;
                }

                if (!empty($objCardConnection->ConnectionData) && is_a($objCard->ConnectionData,"CardConnectionModel"))
                {
                    $objUpdatedConnectionResult = ConnectionsModule::Update($objCardConnection->ConnectionData);

                    if ( $objUpdatedConnectionResult->Result->Success === false)
                    {
                        return $objUpdatedConnectionResult;
                    }
                }
            }
        }

        return $this->getById($objCard->card_id);
    }

    public function ReplaceCarotsWithCustomerData($strContent, $objCustomer, $objCustomerConnection) : string
    {
        $objCarotData = array(
            "[EZcarot_CardId]",
            "[EZcarot_FirstName]",
            "[EZcarot_LastName]",
            "[EZcarot_Phone]",
            "[EZcarot_SMS]",
            // 36
            "[EZcarot_Email]",
            "[EZcarot_BusinessName]",
            "[EZcarot_Title]",
            "[EZcarot_Website1]",
            // 7
            "[EZcarot_Website2]",
            // 21
            "[EZcarot_Website3]",
            // 27
            "[EZcarot_Website4]",
            // 30
            "[EZcarot_Facebook]",
            // 9
            "[EZcarot_GooglePlus]",
            // 10
            "[EZcarot_Instagram]",
            // 11
            "[EZcarot_LinkedIn]",
            // 12
            "[EZcarot_Pinterest]",
            // 13
            "[EZcarot_Snapchat]",
            // 14
            "[EZcarot_Twitter]",
            // 15
            "[EZcarot_Username]"
        );

        $objCarotReplace = array(
            $objCustomer["id"],
            $objCustomer["userFname"],
            $objCustomer["userLname"],
            $objCustomerConnection["cell_phone"],
            $objCustomerConnection["sms_phone"],
            $objCustomerConnection["email_primary"],
            $objCustomer["businessName"],
            $objCustomer["title"],
            $objCustomerConnection["website_1"],
            $objCustomerConnection["website_2"],
            $objCustomerConnection["website_3"],
            $objCustomerConnection["website_4"],
            $objCustomerConnection["facebook"],
            $objCustomerConnection["google_plus"],
            $objCustomerConnection["instagram"],
            $objCustomerConnection["linkedin"],
            $objCustomerConnection["pinterest"],
            $objCustomerConnection["snapchat"],
            $objCustomerConnection["twitter"],
            $objCustomer["username"]
        );

        $strContent = str_replace($objCarotData, $objCarotReplace, $strContent);

        return $strContent;
    }

    public function getOrderLineByCardId($id) : ExcellTransaction
    {
        if (!(new OrderLineModel())->Add("order_line_id", $id))
        {
            return new ExcellTransaction(false, "The value passed in for order_line_id did not pass validation: " . $id);
        }

        $objWhereClause = "
            SELECT ol.*
            FROM `excell_main`.`card` cd
            LEFT JOIN `excell_crm`.`order_line` ol ON ol.order_line_id = cd.order_line_id
            WHERE cd.card_id IN ({$id})";

        $paymentHistoryResult = Database::getSimple($objWhereClause, "order_line_id");
        $paymentHistoryResult->getData()->HydrateModelData(OrderLineModel::class, true);

        return $paymentHistoryResult;
    }

    public function buildCardWhereClauseWithIds(array $ids, $filterIdField = null, $filterEntity = null) : string
    {
        $objWhereClause = $this->cardListPrimaryDataForDisplay($filterIdField, $filterEntity);

        if ($filterEntity !== null)
        {
            $objWhereClause .= "AND card.card_id IN (".implode(",", $ids).")"; // 9 = card affiliate
        }

        if (!in_array($this->app->getActiveLoggedInUser()->user_id, [1000, 1001, 90990]))
        {
            $objWhereClause .= " AND (card.template_card = 0) ";
        }

        $objWhereClause .= " GROUP BY(card.card_id) ORDER BY card.card_num DESC";

        return $objWhereClause;
    }

    public function buildCardBatchWhereClause($filterIdField = null, $filterEntity = null, int $typeId = 1) : string
    {
        $objWhereClause = $this->cardListPrimaryDataForDisplay($filterIdField, $filterEntity);

        if ($filterEntity !== null)
        {
            $objWhereClause .= "AND ( (cowner.{$filterIdField} = {$filterEntity} OR cuser.{$filterIdField} = {$filterEntity})";
            $objWhereClause .= " OR (card_rel.{$filterIdField} = {$filterEntity} AND card_rel.status = 'Active') AND card_rel.card_rel_type_id != 9)"; // 9 = card affiliate
        }

        if (!in_array($this->app->getActiveLoggedInUser()->user_id, [1000, 1001, 90990]))
        {
            $objWhereClause .= " AND (card.template_card = 0) ";
        }

        $objWhereClause .= " AND card_type_id = {$typeId}";
        $objWhereClause .= " GROUP BY(card.card_id) ORDER BY card.card_num DESC";

        return $objWhereClause;
    }

    private function cardListPrimaryDataForDisplay($filterIdField = null, $filterEntity = null)
    {
        $objWhereClause = "SELECT card.*,
            (SELECT platform_name FROM `excell_main`.`company` WHERE company.company_id = card.company_id LIMIT 1) AS platform, 
            (SELECT url FROM `excell_media`.`image` WHERE image.entity_id = card.card_id AND image.entity_name = 'card' AND image_class = 'main-image' ORDER BY image_id DESC LIMIT 1) AS banner, 
            (SELECT thumb FROM `excell_media`.`image` WHERE image.entity_id = card.card_id AND image.entity_name = 'card' AND image_class = 'favicon-image' ORDER BY image_id DESC LIMIT 1) AS favicon,
            (SELECT CONCAT(user.first_name, ' ', user.last_name) FROM `excell_main`.`user` WHERE user.user_id = card.owner_id LIMIT 1) AS card_owner_name,
            (SELECT CONCAT(user.first_name, ' ', user.last_name) FROM `excell_main`.`user` WHERE user.user_id = card.card_user_id LIMIT 1) AS card_user_name,
            (SELECT title FROM `excell_main`.`product` WHERE product.product_id = card.product_id LIMIT 1) AS product, 
            (SELECT COUNT(*) FROM `excell_main`.`contact_card_rel` mcgr WHERE mcgr.card_id = card.card_id) AS card_contacts
            FROM excell_main.card ";

        if ($filterEntity !== null)
        {
            $objWhereClause .= "LEFT JOIN `excell_main`.`user` cowner ON cowner.user_id = card.owner_id ";
            $objWhereClause .= "LEFT JOIN `excell_main`.`user` cuser ON cuser.user_id = card.card_user_id ";
            $objWhereClause .= "LEFT JOIN `excell_main`.`card_rel` ON card_rel.card_id = card.card_id ";
        }

        $objWhereClause .= "WHERE card.company_id = {$this->app->objCustomPlatform->getCompanyId()} AND card.status != 'Deleted' ";

        return $objWhereClause;
    }
}

