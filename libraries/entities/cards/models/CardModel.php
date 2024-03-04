<?php

namespace Entities\Cards\Models;

use App\Core\AppModel;
use App\Utilities\Database;
use App\Utilities\Excell\ExcellCollection;
use Entities\Cards\Classes\CardDomains;
use Entities\Cards\Classes\CardPage;
use Entities\Cards\Classes\CardSettings;
use Entities\Cards\Classes\CardSocialMedia;
use Entities\Cards\Classes\CardTemplates;
use Entities\Mobiniti\Classes\MobinitiContacts;
use Entities\Modules\Classes\AppInstanceRels;
use Entities\Modules\Classes\AppInstanceRelSettings;
use Entities\Modules\Models\AppInstanceRelModel;
use Entities\Modules\Models\AppInstanceRelSettingModel;
use Entities\Payments\Models\ArInvoiceModel;
use Entities\Payments\Models\PaymentAccountModel;
use Entities\Users\Classes\Connections;
use Entities\Users\Classes\UserAddress;
use Entities\Users\Classes\Users;

/**
 * @property int $card_id
 * @property int $owner_id
 * @property int $card_user_id
 * @property int $division_id
 * @property int $company_id
 * @property int $card_version_id
 * @property int $card_type_id
 * @property string $card_name
 * @property string $status
 * @property bool $template_card
 * @property int $order_line_id
 * @property int $product_id
 * @property int $template_id
 * @property string $card_vanity_url
 * @property int $phone_addon_id
 * @property string $card_keyword
 * @property int $card_num
 * @property int $redirect_to
 * @property string $card_data
 * @property string $created_on
 * @property int $created_by
 * @property string $last_updated
 * @property int $updated_by
 * @property string $sys_row_id
 */

class CardModel extends AppModel
{
    protected string $EntityName = "Cards";
    protected string $ModelName = "Card";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions(): array
    {
        return [
            "card_id" => [ "type" => "int", "length" => 15],
            "owner_id" => [ "type" => "int", "length" => 15, "fk" => [  "table" => "user",  "key" => "user_id",  "value" => "username" ]],
            "card_user_id" => [ "type" => "int", "length" => 15, "fk" => [  "table" => "user",  "key" => "user_id",  "value" => "username" ]],
            "division_id" => [ "type" => "int", "length" => 15, "fk" => [  "table" => "division",  "key" => "division_id",  "value" => "division_name" ]],
            "company_id" => [ "type" => "int", "length" => 15, "fk" => [  "table" => "company",  "key" => "company_id",  "value" => "company_name" ]],
            "card_version_id" => [ "type" => "int", "length" => 15, "nullable" => true],
            "card_type_id" => [ "type" => "int", "length" => 5, "fk" => [  "table" => "card_type",  "key" => "card_type_id",  "value" => "name" ]],
            "card_name" => [ "type" => "varchar", "length" => 255],
            "status" => [ "type" => "varchar", "length" => 15],
            "template_card" => [ "type" => "boolean"],
            "order_line_id" => [ "type" => "int", "length" => 15],
            "product_id" => [ "type" => "int", "length" => 15, "fk" => [  "table" => "product",  "key" => "product_id",  "value" => "title" ]],
            "template_id" => [ "type" => "int", "length" => 15, "fk" => [  "table" => "card_template",  "key" => "card_template_id",  "value" => "name" ]],
            "card_vanity_url" => [ "type" => "varchar", "length" => 25],
            "phone_addon_id" => [ "type" => "int", "length" => 15, "nullable" => true],
            "card_keyword" => [ "type" => "varchar", "length" => 50],
            "card_num" => [ "type" => "int", "length" => 15],
            "redirect_to" => [ "type" => "int", "length" => 5, "nullable" => true],
            "card_data" => [ "type" => "json", "length" => 0],
            "created_on" => [ "type" => "datetime", "length" => 0],
            "created_by" => [ "type" => "int", "length" => 15, "fk" => [  "table" => "user",  "key" => "user_id",  "value" => "username" ]],
            "last_updated" => [ "type" => "datetime", "length" => 0],
            "updated_by" => [ "type" => "int", "length" => 15, "fk" => [  "table" => "user",  "key" => "user_id",  "value" => "username" ]],
            "sys_row_id" => [ "type" => "char", "length" => 36]
        ];
    }

    public function LoadFullCard($transformCarots = true): void
    {
        $this->LoadCardOwner();
        $this->LoadCardConnections(true);
        $this->LoadCardAddress();
        $this->LoadCardPages($transformCarots);
        $this->LoadCardSettings();
        $this->FactorStyleValues();
    }

    public function LoadCardOwner(): static
    {
        $this->AddUnvalidatedValue("Owner", (new Users())->getFks(["user_email", "user_phone"])->getById($this->owner_id)->getData()->first());

        if (!empty($this->card_user_id))
        {
            $this->AddUnvalidatedValue("CardUser", (new Users())->getFks(["user_email", "user_phone"])->getById($this->card_user_id)->getData()->first());
        }

        return $this;
    }

    public function LoadCardPages($blnTransformCarots = true, $includeContent = true) : void
    {
        $objCardPagesModule = new CardPage();
        $objCardPageResult = $objCardPagesModule->GetByCardId($this->card_id);

        $objModuleApp = new AppInstanceRels();
        $objCardWidgets = $objModuleApp->getByPageIds($objCardPageResult->getData()->FieldsToArray(["card_tab_id"]));

        $objCardPageResult->getData()->HydrateChildModelData("__app", ["card_page_rel_id" => "card_tab_rel_id"], $objCardWidgets->getData(), true);

        if ($objCardPageResult->result->Success === true)
        {
            $objModuleAppWidgetSettings = new AppInstanceRelSettings();
            $widgetSettings = $objModuleAppWidgetSettings->getByInstanceRelIds($objCardWidgets->getData()->FieldsToArray(["app_instance_rel_id"]));

            $objCardPageResult->getData()->Foreach(function(CardPageRelModel $appInstanceRelModel) use ($widgetSettings) {
                if (empty($appInstanceRelModel->__app)) {
                    return false;
                }

                $appInstanceRelId = $appInstanceRelModel->__app->app_instance_rel_id;
                $appInstanceRelModel->__app->AddUnvalidatedValue("__settings", $widgetSettings->getData()->FindMatching(function(AppInstanceRelSettingModel $setting) use ($appInstanceRelId) {
                    if ($setting->app_instance_rel_id !== $appInstanceRelId) {
                        return false;
                    }
                    return $setting->ToPublicArray(["label", "value"]);
                }));

                return $appInstanceRelModel;
            });

            if ($includeContent === false)
            {
                $objCardPageResult->getData()->Foreach(function($currPage)
                {
                    $currPage->AddUnvalidatedValue("content", null);
                    return $currPage;
                });
            }

            if ($blnTransformCarots === false)
            {
                $this->AddUnvalidatedValue("Tabs", $objCardPageResult->data);
            }
            else
            {
                $this->LoadCardConnections(true);

                foreach($objCardPageResult->data as $intCardPageIndex => $objCardPage)
                {
                    /** Not a TemplateFile Request */
                    if ( $objCardPage->card_tab_type_id != 2)
                    {
                        $objCardPageResult->getData()->{$intCardPageIndex}->content = $objCardPagesModule->ReplaceCarotsWithCustomerData($objCardPage->content, $this, $this->Owner, $this->Connections, $this->Addresses);
                    }
                }

                $this->AddUnvalidatedValue("Tabs", $objCardPageResult->data);
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
            case "availablePublicModules":
                $this->LoadAvailablePublicModules();
                break;
            case "modules":
                $this->LoadModules();
                break;
        }

        $this->LoadCardSettings(true);
    }

    protected function LoadPaymentAccount() : void
    {
        $objWhereClause = "
            SELECT pa.*
            FROM `excell_financial`.`payment_account` pa 
            LEFT JOIN `excell_crm`.`order_line` ol ON ol.payment_account_id = pa.payment_account_id
            WHERE ol.order_line_id IN ({$this->order_line_id}) LIMIT 1";

        $paymentAccountResult = Database::getSimple($objWhereClause, "card_num");
        $paymentAccountResult->getData()->HydrateModelData(PaymentAccountModel::class, true);

        $this->AddUnvalidatedValue("PaymentAccount", $paymentAccountResult->getData()->first());
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
        $paymentHistoryResult->getData()->HydrateModelData(ArInvoiceModel::class, true);

        $this->AddUnvalidatedValue("PaymentHistory", $paymentHistoryResult->data);
    }

    protected function LoadModules() : void
    {
        $objWhereClause = "
            SELECT apr.*, ai.instance_uuid, ai.module_app_id, air.module_app_widget_id, ma.logo, mo.name AS module_name, mo.category AS module_class, mo.version AS module_version
            FROM `excell_main`.`app_instance_rel` apr
            LEFT JOIN `excell_main`.`app_instance` ai ON ai.app_instance_id = apr.app_instance_id
            LEFT JOIN `excell_modules`.`module_apps` ma ON ma.module_app_id = ai.module_app_id
            LEFT JOIN `excell_modules`.`modules` mo ON mo.module_id = ma.module_id
            WHERE apr.card_id = ({$this->card_id})";

        $paymentHistoryResult = Database::getSimple($objWhereClause, "app_instance_rel_id");

        $paymentHistoryResult->getData()->HydrateModelData(AppInstanceRelModel::class, true);

        $this->AddUnvalidatedValue("Modules", $paymentHistoryResult->data);
    }

    protected function LoadAvailablePublicModules() : void
    {
        $objWhereClause = "SELECT
                maw.module_app_widget_id AS app_widget_id,
                maw.module_app_id AS app_id,
                ma.name AS app_name,
                mawc.label AS widget_class,
                mawc.tag AS widget_tag,
                maw.name AS widget_name,
                maw.endpoint AS widget_endpoint,
                ma.domain AS widget_domain,
                maw.version AS widget_version,
                ai.app_instance_id AS app_instance_id,
                ai.instance_uuid
            FROM `excell_main`.`app_instance` ai
            CROSS JOIN `excell_modules`.`module_app_widgets` maw
            LEFT JOIN `excell_modules`.`module_app_widget_class` mawc ON mawc.module_app_widget_class_id = maw.widget_class 
            LEFT JOIN `excell_modules`.`module_apps` ma ON ma.module_app_id = ai.module_app_id
            WHERE ai.owner_id = ({$this->owner_id}) AND maw.module_app_id = ai.module_app_id AND mawc.tag = 'public-page'";

        $availablePublicModuleWidgets = Database::getSimple($objWhereClause, "app_widget_id");

        $availablePublicModuleWidgets->getData()->HydrateModelData(AppInstanceRelModel::class, true);

        $this->AddUnvalidatedValue("AvailablePublicModules", $availablePublicModuleWidgets->data);
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
        $this->AddUnvalidatedValue("Addresses", $objAddressResult->data);
        return $this;
    }

    public function LoadCardSocialMedia($fks) : void
    {
        if (!empty($this->SocialMedia) && is_a($this->SocialMedia, \App\Utilities\Excell\ExcellCollection::class))
        {
            return;
        }

        $colCardSocialMediaResult = (new CardSocialMedia())->getByCardId($this->card_id);

        $this->AddUnvalidatedValue("SocialMedia", $colCardSocialMediaResult->data);
    }

    public function LoadCardDomains() : void
    {
        $cardDomans = new CardDomains();
        $cardDomain = $cardDomans->getWhere(["card_id" => $this->getId()])->getData()->first();

        if (empty($cardDomain)) {
            return;
        }

        $this->AddUnvalidatedValue("card_domain", $cardDomain->domain_name);
        $this->AddUnvalidatedValue("card_domain_ssl", $cardDomain->ssl);
    }

    public function LoadCardConnections($fks) : void
    {
        if (!empty($this->Connections) && is_a($this->Connections, \App\Utilities\Excell\ExcellCollection::class)) {
            return;
        }

        if (!is_a($this->Template, CardTemplateModel::class))  {
            $this->LoadCardTemplate();
        }

        $colCardConnectionsResult = (new Connections())->getByCardId($this->card_id);
        $colCardDisplayConnections = new ExcellCollection();

        if (!empty($this->Template->data->connections->count) && $this->Template->data->connections->count > 0) {
            for($intConnectionIndex = 1; $intConnectionIndex <= $this->Template->data->connections->count; $intConnectionIndex++) {
                $objConnection = $colCardConnectionsResult->getData()->FindEntityByValue("display_order", $intConnectionIndex);

                if ( $objConnection !== null) {
                    $colCardDisplayConnections->Add($intConnectionIndex, $objConnection);
                } else {
                    $objBlankConnection = new CardConnectionModel();
                    $objBlankConnection->AddUnvalidatedValue('card_id', $this->card_id);
                    $objBlankConnection->AddUnvalidatedValue('display_order', $intConnectionIndex);
                    $objBlankConnection->AddUnvalidatedValue('connection_type_id',0);
                    $objBlankConnection->AddUnvalidatedValue('connection_type_name','blank');
                    $objBlankConnection->AddUnvalidatedValue('font_awesome',"fas fa-question");
                    $colCardDisplayConnections->Add($intConnectionIndex, $objBlankConnection);
                }
            }
        }

        $this->AddUnvalidatedValue("Connections", $colCardDisplayConnections);
    }

    public function LoadCardContacts() : void
    {
        $colCardContacts = (new MobinitiContacts())->GetByCardId($this->card_id)->getData();
        $this->AddUnvalidatedValue("Contacts", $colCardContacts);
    }

    public function LoadCardTemplate() : void
    {
        $objCardTemplate = (new CardTemplates())->getById($this->template_id);

        if ($objCardTemplate->result->Success === true) {
            $this->AddUnvalidatedValue("Template", $objCardTemplate->getData()->first());
        }
    }

    public function LoadCardSettings($public = true) : ExcellCollection
    {
        if (empty($this->CardUser)) {
            $this->LoadCardOwner();
        }
        $cardSettings = new CardSettings();
        $cardSettingsResult = $cardSettings->getWhere(["card_id" => $this->card_id]);
        $cardSettingsCollection = $cardSettingsResult->getData();

        if ($this->card_type_id === 2) {
            if (empty($cardSettingsCollection->FindEntityByValue("label", "display_name")->value)) {
                $cardSettingsCollection->Add(new CardSettingModel(["label" => "display_name", "tags" => "card", "value" => $this->CardUser->first_name . " " . $this->CardUser->last_name]));
            }
            if (empty($cardSettingsCollection->FindEntityByValue("label", "contact_phone")->value)) {
                $cardSettingsCollection->Add(new CardSettingModel(["label" => "contact_phone", "tags" => "card", "value" => $this->CardUser->user_phone]));
            }
            if (empty($cardSettingsCollection->FindEntityByValue("label", "contact_email")->value)) {
                $cardSettingsCollection->Add(new CardSettingModel(["label" => "contact_email", "tags" => "card", "value" => $this->CardUser->user_email]));
            }
        }

        if ($public === true) {
            $cardImagesArray = [];
            $cardLogosArray = [];
            $cardSettingsArray = [];
            $cardSettingsCollection->Foreach(function(CardSettingModel $currSetting) use (&$cardSettingsArray, &$cardImagesArray, &$cardLogosArray) {
                if (in_array($currSetting->tags, ["image", "gradient", "color"])) {
                    if (str_starts_with($currSetting->label, "background___")) {
                        $cardImagesArray[str_replace("background___", "", $currSetting->label)] =  $currSetting->tags . "|" . $currSetting->value . "|" . $currSetting->options;
                    } elseif (str_starts_with($currSetting->label, "logo___")) {
                        $cardLogosArray[str_replace("logo___", "", $currSetting->label)] = $currSetting->tags . "|" . $currSetting->value . "|" . $currSetting->options;
                    } else {
                        $cardSettingsArray[$currSetting->label] = $currSetting->tags . "|" . $currSetting->value . "|" . $currSetting->options;
                    }
                } else {
                    if (str_ends_with($currSetting->label, "_config")) {
                        $cardSettingsArray[$currSetting->label] = json_decode($currSetting->value, true);
                    } else {
                        $cardSettingsArray[$currSetting->label] = $currSetting->value;
                    }
                }
            });

            $this->AddUnvalidatedValue("Settings", $cardSettingsArray);
            $this->AddUnvalidatedValue("Media", $cardImagesArray);
            $this->AddUnvalidatedValue("Logos", $cardLogosArray);
        } else {
            $cardImagesCol = new ExcellCollection();
            $cardLogosCol = new ExcellCollection();
            $cardSettingsCol = new ExcellCollection();
            $cardSettingsCollection->Foreach(function(CardSettingModel $currSetting) use (&$cardSettingsCol, &$cardImagesCol, &$cardLogosCol) {
                if (str_starts_with($currSetting->label, "background___")) {
                    $cardImagesCol->Add($currSetting);
                } elseif (str_starts_with($currSetting->label, "logo___")) {
                    $cardLogosCol->Add($currSetting);
                } else {
                    $cardSettingsCol->Add($currSetting);
                }
            });
            $this->AddUnvalidatedValue("Settings", $cardSettingsCol);
            $this->AddUnvalidatedValue("Media", $cardImagesCol);
            $this->AddUnvalidatedValue("Logos", $cardLogosCol);
        }

        return $cardSettingsCollection;
    }

    public function LoadCardImages() : void
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
        $cardResult->getData()->HydrateModelData(CardModel::class, true);

        $card = $cardResult->getData()->first();

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

    public function FactorStyleValues() : void
    {
        if (empty($this->card_data))
        {
            $this->AddUnvalidatedValue("card_data", new \stdClass());
        }

        if (empty($this->card_data->style))
        {
            $this->card_data->style = new \stdClass();
        }

        if (empty($this->card_data->style->card))
        {
            $this->card_data->style->card = new \stdClass();
        }

        if (empty($this->card_data->style->card->color))
        {
            $this->card_data->style->card->color = new \stdClass();
        }

        if (empty($this->card_data->style->card->color->main_rgb))
        {
            $this->card_data->style->card->color->main_rgb = new \stdClass();
        }

//        $this->card_data->style->card->color->main_rgb->red = hexdec(substr($this->card_data->style->card->color->main ?? '000000',0,2)) ?? "00";
//        $this->card_data->style->card->color->main_rgb->green = hexdec(substr($this->card_data->style->card->color->main ?? '000000',2,2)) ?? "00";
//        $this->card_data->style->card->color->main_rgb->blue = hexdec(substr($this->card_data->style->card->color->main ?? '000000',4,2)) ?? "00";

        if (empty($this->card_data->style->card->font->main->card_font_id))
        {
            $strCardFontQuery = "SELECT * FROM card_font WHERE card_font_id = " . ($this->card_data->style->card->font->main ?? "7") . " LIMIT 1";

            Database::ResetDbConnection();
            $objCardFontResult = Database::getSimple($strCardFontQuery);

            if ( $objCardFontResult->result->Count === 1 )
            {
                if (empty($this->card_data->style->card->font))
                {
                    $this->card_data->style->card->font= new \stdClass();
                }

                $this->card_data->style->card->font->main = $objCardFontResult->getData()->first();
            }
        }
    }
}
