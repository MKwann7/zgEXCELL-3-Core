<?php

namespace Entities\Cards\Models;

use App\Core\AppModel;
use App\Utilities\Database;
use App\Utilities\Excell\ExcellCollection;
use Entities\Cards\Classes\CardPage;
use Entities\Cards\Classes\CardSocialMedia;
use Entities\Cards\Classes\CardTemplates;
use Entities\Mobiniti\Classes\MobinitiContacts;
use Entities\Modules\Classes\AppInstanceRels;
use Entities\Modules\Models\AppInstanceRelModel;
use Entities\Payments\Models\ArInvoiceModel;
use Entities\Payments\Models\PaymentAccountModel;
use Entities\Users\Classes\Connections;
use Entities\Users\Classes\UserAddress;
use Entities\Users\Classes\Users;

class CardModel extends AppModel
{
    protected $EntityName = "Cards";
    protected $ModelName = "Card";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "card_id" => [ "type" => "int", "length" => 15],
            "owner_id" => [ "type" => "int", "length" => 15, "fk" => [  "table" => "user",  "key" => "user_id",  "value" => "username" ]],
            "card_user_id" => [ "type" => "int", "length" => 15, "fk" => [  "table" => "user",  "key" => "user_id",  "value" => "username" ]],
            "division_id" => [ "type" => "int", "length" => 15, "fk" => [  "table" => "division",  "key" => "division_id",  "value" => "division_name" ]],
            "company_id" => [ "type" => "int", "length" => 15, "fk" => [  "table" => "company",  "key" => "company_id",  "value" => "company_name" ]],
            "card_version_id" => [ "type" => "int", "length" => 15],
            "card_type_id" => [ "type" => "int", "length" => 5, "fk" => [  "table" => "card_type",  "key" => "card_type_id",  "value" => "name" ]],
            "card_name" => [ "type" => "varchar", "length" => 255],
            "status" => [ "type" => "varchar", "length" => 15],
            "template_card" => [ "type" => "boolean"],
            "order_line_id" => [ "type" => "int", "length" => 15],
            "product_id" => [ "type" => "int", "length" => 15, "fk" => [  "table" => "product",  "key" => "product_id",  "value" => "title" ]],
            "template_id" => [ "type" => "int", "length" => 15, "fk" => [  "table" => "card_template",  "key" => "card_template_id",  "value" => "name" ]],
            "card_vanity_url" => [ "type" => "varchar", "length" => 25],
            "card_keyword" => [ "type" => "varchar", "length" => 50],
            "card_num" => [ "type" => "int", "length" => 15],
            "redirect_to" => [ "type" => "int", "length" => 5],
            "card_data" => [ "type" => "json", "length" => 0],
            "created_on" => [ "type" => "datetime", "length" => 0],
            "created_by" => [ "type" => "int", "length" => 15, "fk" => [  "table" => "user",  "key" => "user_id",  "value" => "username" ]],
            "last_updated" => [ "type" => "datetime", "length" => 0],
            "updated_by" => [ "type" => "int", "length" => 15, "fk" => [  "table" => "user",  "key" => "user_id",  "value" => "username" ]],
            "sys_row_id" => [ "type" => "char", "length" => 36]
        ];
    }

    public function LoadFullCard($transformCarots = true)
    {
        $this->LoadCardOwner();
        $this->LoadCardConnections(true);
        $this->LoadCardAddress();
        $this->LoadCardPages($transformCarots);
        $this->LoadCardImages();
        $this->FactorStyleValues();
    }

    public function LoadCardOwner()
    {
        $this->AddUnvalidatedValue("Owner", (new Users())->getFks(["user_email", "user_phone"])->getById($this->owner_id)->Data->First());

        if (!empty($this->card_user_id))
        {
            $this->AddUnvalidatedValue("CardUser", (new Users())->getFks(["user_email", "user_phone"])->getById($this->card_user_id)->Data->First());
        }

        return $this;
    }

    public function LoadCardPages($blnTransformCarots = true, $includeContent = true) : void
    {
        $objCardPagesModule = new CardPage();
        $objCardPageResult = $objCardPagesModule->GetByCardId($this->card_id);

        $objModuleApp = new AppInstanceRels();
        $objCardWidgets = $objModuleApp->getByPageIds($objCardPageResult->Data->FieldsToArray(["card_tab_id"]));

        $objCardPageResult->Data->HydrateChildModelData("__app", ["card_page_rel_id" => "card_tab_rel_id"], $objCardWidgets->Data, true);

        if ($objCardPageResult->Result->Success === true)
        {
            if ($includeContent === false)
            {
                $objCardPageResult->Data->Foreach(function($currPage)
                {
                    $currPage->AddUnvalidatedValue("content", null);
                    return $currPage;
                });
            }

            if ($blnTransformCarots === false)
            {
                $this->AddUnvalidatedValue("Tabs", $objCardPageResult->Data);
            }
            else
            {
                $this->LoadCardConnections(true);

                foreach($objCardPageResult->Data as $intCardPageIndex => $objCardPage)
                {
                    /** Not a TemplateFile Request */
                    if ( $objCardPage->card_tab_type_id != 2)
                    {
                        $objCardPageResult->Data->{$intCardPageIndex}->content = $objCardPagesModule->ReplaceCarotsWithCustomerData($objCardPage->content, $this, $this->Owner, $this->Connections, $this->Addresses);
                    }
                }

                $this->AddUnvalidatedValue("Tabs", $objCardPageResult->Data);
            }
        }
    }

    public function LoadAddons($addon) : void
    {
        switch($addon)
        {
            case "paymentAccount":
                $this->LoadPaymentAccount();
                break;
            case "paymentHistory":
                $this->LoadPaymentHistory();
                break;
            case "modules":
                $this->LoadModules();
                break;
        }
    }

    protected function LoadPaymentAccount() : void
    {
        $objWhereClause = "
            SELECT pa.*
            FROM `excell_financial`.`payment_account` pa 
            LEFT JOIN `excell_crm`.`order_line` ol ON ol.payment_account_id = pa.payment_account_id
            WHERE ol.order_line_id IN ({$this->order_line_id}) LIMIT 1";

        $paymentAccountResult = Database::getSimple($objWhereClause, "card_num");
        $paymentAccountResult->Data->HydrateModelData(PaymentAccountModel::class, true);

        $this->AddUnvalidatedValue("PaymentAccount", $paymentAccountResult->Data->First());
    }

    protected function LoadPaymentHistory() : void
    {
        $objWhereClause = "
            SELECT ar.*,
            (SELECT CONCAT(ur.first_name, ' ', ur.last_name) FROM `excell_main`.`user` ur WHERE ur.user_id = ar.user_id LIMIT 1) AS payment_user
            FROM `excell_financial`.`transaction` ta
            RIGHT JOIN `excell_financial`.`ar_invoice` ar ON ar.ar_invoice_id = ta.ar_invoice_id
            WHERE ta.order_line_id IN ({$this->order_line_id})";

        $paymentHistoryResult = Database::getSimple($objWhereClause, "ar_invoice_id");
        $paymentHistoryResult->Data->HydrateModelData(ArInvoiceModel::class, true);

        $this->AddUnvalidatedValue("PaymentHistory", $paymentHistoryResult->Data);
    }

    protected function LoadModules() : void
    {
        $objWhereClause = "
            SELECT apr.*, ai.instance_uuid, ai.module_app_id, ai.module_app_widget_id, ma.logo, mo.name AS module_name, mo.category AS module_class, mo.version AS module_version
            FROM `excell_main`.`app_instance_rel` apr
            LEFT JOIN `excell_main`.`app_instance` ai ON ai.app_instance_id = apr.app_instance_id
            LEFT JOIN `excell_modules`.`module_apps` ma ON ma.module_app_id = ai.module_app_id
            LEFT JOIN `excell_modules`.`modules` mo ON mo.module_id = ma.module_id
            WHERE apr.card_id = ({$this->card_id})";

        $paymentHistoryResult = Database::getSimple($objWhereClause, "app_instance_rel_id");

        $paymentHistoryResult->Data->HydrateModelData(AppInstanceRelModel::class, true);

        $this->AddUnvalidatedValue("Modules", $paymentHistoryResult->Data);
    }

    public function removeHiddenPages() : void
    {
        $cardPagesForDeletion = [];

        foreach($this->Tabs as $intCardPageIndex => $objCardPage)
        {
            if ( $objCardPage->rel_visibility === "0" || $objCardPage->visibility === "0")
            {
                $cardPagesForDeletion[] = $intCardPageIndex;
            }
        }

        if (count($cardPagesForDeletion) > 0)
        {
            foreach($cardPagesForDeletion as $currPageIndex)
            {
                $this->Tabs->DeleteByKey($currPageIndex);
            }
        }
    }

    public function LoadCardAddress() : self
    {
        $objAddressResult = (new UserAddress())->getWhere(["user_id" => $this->owner_id],"is_primary.DESC");
        $this->AddUnvalidatedValue("Addresses", $objAddressResult->Data);
        return $this;
    }

    public function LoadCardSocialMedia($fks) : void
    {
        if (!empty($this->SocialMedia) && is_a($this->SocialMedia, \App\Utilities\Excell\ExcellCollection::class))
        {
            return;
        }

        $colCardSocialMediaResult = (new CardSocialMedia())->getByCardId($this->card_id);

        $this->AddUnvalidatedValue("SocialMedia", $colCardSocialMediaResult->Data);
    }

    public function LoadCardConnections($fks) : void
    {
        if (!empty($this->Connections) && is_a($this->Connections, \App\Utilities\Excell\ExcellCollection::class))
        {
            return;
        }

        if (!is_a($this->Template, "CardTemplateModel"))
        {
            $this->LoadCardTemplate();
        }

        $colCardConnectionsResult = (new Connections())->getByCardId($this->card_id);

        $colCardDisplayConnections = new ExcellCollection();

        for($intConnectionIndex = 1; $intConnectionIndex <= $this->Template->data->connections->count; $intConnectionIndex++)
        {
            $objConnection = $colCardConnectionsResult->Data->FindEntityByValue("display_order", $intConnectionIndex);

            if ( $objConnection !== null)
            {
                $colCardDisplayConnections->Add($intConnectionIndex, $objConnection);
            }
            else
            {
                $objBlankConnection = new CardConnectionModel();
                $objBlankConnection->AddUnvalidatedValue('card_id', $this->card_id);
                $objBlankConnection->AddUnvalidatedValue('display_order', $intConnectionIndex);
                $objBlankConnection->AddUnvalidatedValue('connection_type_id',0);
                $objBlankConnection->AddUnvalidatedValue('connection_type_name','blank');
                $objBlankConnection->AddUnvalidatedValue('font_awesome',"fas fa-question");
                $colCardDisplayConnections->Add($intConnectionIndex, $objBlankConnection);
            }
        }

        $this->AddUnvalidatedValue("Connections", $colCardDisplayConnections);
    }

    public function LoadCardContacts()
    {
        $colCardContacts = (new MobinitiContacts())->GetByCardId($this->card_id)->Data;
        $this->AddUnvalidatedValue("Contacts", $colCardContacts);
    }

    public function LoadCardTemplate()
    {
        $objCardTemplate = (new CardTemplates())->getById($this->template_id);

        if ($objCardTemplate->Result->Success === true)
        {
            $this->AddUnvalidatedValue("Template", $objCardTemplate->Data->First());
        }
    }

    public function LoadCardImages()
    {
        $objWhereClause = "
            SELECT card.card_id,
            (SELECT url FROM `excell_media`.`image` WHERE image.entity_id = card.card_id AND image.entity_name = 'card' AND image_class = 'main-image' ORDER BY image_id DESC LIMIT 1) AS banner, 
            (SELECT thumb FROM `excell_media`.`image` WHERE image.entity_id = card.card_id AND image.entity_name = 'card' AND image_class = 'main-image' ORDER BY image_id DESC LIMIT 1) AS banner_thumb, 
            (SELECT url FROM `excell_media`.`image` WHERE image.entity_id = card.card_id AND image.entity_name = 'card' AND image_class = 'favicon-image' ORDER BY image_id DESC LIMIT 1) AS favicon,
            (SELECT thumb FROM `excell_media`.`image` WHERE image.entity_id = card.card_id AND image.entity_name = 'card' AND image_class = 'favicon-image' ORDER BY image_id DESC LIMIT 1) AS ico,
            (SELECT url FROM `excell_media`.`image` WHERE image.entity_id = card.card_id AND image.entity_name = 'card' AND image_class = 'user-avatar-image' ORDER BY image_id DESC LIMIT 1) AS user_avatar,
            (SELECT thumb FROM `excell_media`.`image` WHERE image.entity_id = card.card_id AND image.entity_name = 'card' AND image_class = 'user-avatar-image' ORDER BY image_id DESC LIMIT 1) AS user_avatar_thumb,
            (SELECT url FROM `excell_media`.`image` WHERE image.entity_id = card.card_id AND image.entity_name = 'card' AND image_class = 'logo-image' ORDER BY image_id DESC LIMIT 1) AS logo,
            (SELECT thumb FROM `excell_media`.`image` WHERE image.entity_id = card.card_id AND image.entity_name = 'card' AND image_class = 'logo-image' ORDER BY image_id DESC LIMIT 1) AS logo_thumb,
            (SELECT url FROM `excell_media`.`image` WHERE image.entity_id = card.card_id AND image.entity_name = 'card' AND image_class = 'splash-cover-image' ORDER BY image_id DESC LIMIT 1) AS splash_cover,
            (SELECT thumb FROM `excell_media`.`image` WHERE image.entity_id = card.card_id AND image.entity_name = 'card' AND image_class = 'splash-cover-image' ORDER BY image_id DESC LIMIT 1) AS splash_cover_thumb
            FROM `card` ";

        $objWhereClause .= "WHERE card_id = ".$this->card_id."";

        $objWhereClause .= " LIMIT 1";

        $cardResult = Database::getSimple($objWhereClause, "card_num");
        $cardResult->Data->HydrateModelData(CardModel::class, true);

        $card = $cardResult->Data->First();

        $this->AddUnvalidatedValue("banner", $card->banner ?? "/_ez/templates/" . ( $this->template_id__value ?? "1" ) . "/images/mainImage.jpg");
        $this->AddUnvalidatedValue("banner_thumb", $card->banner_thumb  ?? "/_ez/templates/" . ( $this->template_id__value ?? "1" ) . "/images/mainImage.jpg");
        $this->AddUnvalidatedValue("favicon", $card->favicon  ?? "/_ez/templates/" . ( $this->template_id__value ?? "1" ) . "/images/mainImage.jpg");
        $this->AddUnvalidatedValue("ico", $card->ico  ?? "/_ez/templates/" . ( $this->template_id__value ?? "1" ) . "/images/mainImage.jpg");
        $this->AddUnvalidatedValue("logo", $card->logo  ?? "/_ez/templates/" . ( $this->template_id__value ?? "1" ) . "/images/mainImage.jpg");
        $this->AddUnvalidatedValue("user_avatar", $card->user_avatar  ?? "/_ez/templates/" . ( $this->template_id__value ?? "1" ) . "/images/mainImage.jpg");
        $this->AddUnvalidatedValue("user_avatar_thumb", $card->user_avatar_thumb  ?? "/_ez/templates/" . ( $this->template_id__value ?? "1" ) . "/images/mainImage.jpg");
        $this->AddUnvalidatedValue("splash_cover", $card->splash_cover  ?? "/_ez/templates/" . ( $this->template_id__value ?? "1" ) . "/images/mainImage.jpg");
        $this->AddUnvalidatedValue("splash_cover_thumb", $card->splash_cover_thumb  ?? "/_ez/templates/" . ( $this->template_id__value ?? "1" ) . "/images/mainImage.jpg");
    }

    public function FactorStyleValues()
    {
        if (empty($this->card_data->style->card->color))
        {
            $this->card_data->style->card->color = new \stdClass();
        }

        if (empty($this->card_data->style->card->color->main_rgb))
        {
            $this->card_data->style->card->color->main_rgb = new \stdClass();
        }

        $this->card_data->style->card->color->main_rgb->red = hexdec(substr($this->card_data->style->card->color->main ?? '000000',0,2)) ?? "00";
        $this->card_data->style->card->color->main_rgb->green = hexdec(substr($this->card_data->style->card->color->main ?? '000000',2,2)) ?? "00";
        $this->card_data->style->card->color->main_rgb->blue = hexdec(substr($this->card_data->style->card->color->main ?? '000000',4,2)) ?? "00";

        //logText("CardFontAssignment.log",json_encode($this->card_data->style->card));

        if (empty($this->card_data->style->card->font->main->card_font_id))
        {
            $strCardFontQuery = "SELECT * FROM card_font WHERE card_font_id = " . ($this->card_data->style->card->font->main ?? "7") . " LIMIT 1";

            Database::ResetDbConnection();
            $objCardFontResult = Database::getSimple($strCardFontQuery);

            if ( $objCardFontResult->Result->Count === 1 )
            {
                if (empty($this->card_data->style->card->font))
                {
                    $this->card_data->style->card->font= new \stdClass();
                }

                $this->card_data->style->card->font->main = $objCardFontResult->Data->First();
            }
        }
    }
}
