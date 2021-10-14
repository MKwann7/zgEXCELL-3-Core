<?php

namespace Entities\Cards\Classes;

use App\Core\AppController;
use App\Core\AppEntity;
use App\Utilities\Excell\ExcellRelationship;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Cards\Models\CardModel;
use Entities\Cards\Models\CardPageModel;

class CardPage extends AppEntity
{
    public $strEntityName       = "Cards";
    public $strDatabaseTable    = "card_tab";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = CardPageModel::class;
    public $strMainModelPrimary = "card_tab_id";

    public function card_countRelationship() : ExcellRelationship
    {
        return $this->buildRelationshipModel("card_", "Main", "card_tab_rel", "COUNT()", "card_tab_id", "card_tab_id");
    }

    public function createNew($objEntityData) : ExcellTransaction
    {
        $objEntityModelResult = parent::createNew($objEntityData);

        return $objEntityModelResult;
    }

    public function GetByCardId($intCardId) : ExcellTransaction
    {
        $objCardResult = new ExcellTransaction();

        if ( empty($intCardId) || !isInteger($intCardId))
        {
            $objCardResult->Result->Success = false;
            $objCardResult->Result->Count = 0;
            $objCardResult->Result->Message = "You must supply a valid card id.";
            return $objCardResult;
        }

        $objCardPageRelModule = new CardPageRels;

        global $app;
        $objCardRel = null;

        if ($app->objCustomPlatform->getCompanyId() === 4 && in_array($app->getActiveLoggedInUser()->user_id, [1002, 1003, 70726, 73837, 90999, 91003, 91015, 91014]))
        {
            $objCardRel = $objCardPageRelModule->getWhere([["card_id", "=", $intCardId], "AND", ["synced_state", "=", 0]]);
        }
        else
        {
            $objCardRel = $objCardPageRelModule->getWhere(["card_id" => $intCardId]);
        }

        if ($objCardRel->Result->Count === 0)
        {
            return $objCardRel;
        }

        $arCardPageIds = $objCardRel->Data->FieldsToArray(["card_tab_id"]);
        $objCardPages = $this->getWhereIn("card_tab_id", $arCardPageIds);
        $objCardRel->Data->MergeFields($objCardPages->Data,["user_id","title","content","order_number","url","library_tab","visibility","permanent","card_tab_type_id","created_on","last_updated"],["card_tab_id"]);
        $objCardRel->Data->SortBy("rel_sort_order","ASC");

        return $objCardRel;
    }

    public function ReplaceCarotsWithCustomerData($strContent, $objCard, $objOwner, $lstConnections, $lstAddresses)
    {
        return base64_encode(static::ReplaceCarots(base64_decode($strContent), $objCard, $objOwner, $lstConnections, $lstAddresses));
    }

    protected function ReplaceCarots($strContent, CardModel $objCard, $objOwner, $lstConnections, $lstAddresses)
    {
        $objCarotData = array(
            "[EZcarot_CardId]",
            "[EZcarot_Keyword]",
            "[EZcarot_FirstName]",
            "[EZcarot_LastName]",
            "[EZcarot_Phone]",
            "[EZcarot_SMS]",
            "[EZcarot_Email]",
            "[EZcarot_BusinessName]",
            "[EZcarot_Title]",
            "[EZcarot_PreferredName]",
            "[EZcarot_Address_Full_Html]",
            "[EZcarot_Address1]",
            "[EZcarot_Address2]",
            "[EZcarot_City]",
            "[EZcarot_State]",
            "[EZcarot_Zip]",
            "[EZcarot_Country]",
            "[EZcarot_RGB_R]",
            "[EZcarot_RGB_G]",
            "[EZcarot_RGB_B]",
            "[EZcarot_Website1]",
            "[EZcarot_Facebook]",
            "[EZcarot_GooglePlus]",
            "[EZcarot_Instagram]",
            "[EZcarot_LinkedIn]",
            "[EZcarot_Pinterest]",
            "[EZcarot_Snapchat]",
            "[EZcarot_Twitter]",
            "[EZcarot_Vimeo]",
            "[EZcarot_YouTube]",
            "[EZcarot_Bandcamp]",
            "[EZcarot_SoundCloud]",
            "[EZcarot_Twitch]",
            "[EZcarot_Username]"
        );

        $objCard->FactorStyleValues();

        $objCarotReplace = array(
            $objCard->card_num,
            $objCard->card_keyword,
            $objOwner->first_name,
            $objOwner->last_name,
            !empty($lstConnections) ? $lstConnections->FindEntityByValue("connection_type_id","Mobile")->connection_value ?? "" : "",
            !empty($lstConnections) ? $lstConnections->FindEntityByValue("connection_type_id","Mobile")->connection_value ?? "" : "",
            !empty($lstConnections) ? $lstConnections->FindEntityByValue("connection_type_id","E-mail")->connection_value ?? "" : "",
            "", // Build this soon...
            "", // Build this soon...
            $objOwner->preferred_name,
            !empty($lstAddresses->First()) ? $lstAddresses->First()->address_1 . " " . $lstAddresses->First()->address_2 . "<br>" . $lstAddresses->First()->city . ", " . $lstAddresses->First()->state . " " . $lstAddresses->First()->zip : "",
            !empty($lstAddresses->First()) ? $lstAddresses->First()->address_1 : "",
            !empty($lstAddresses->First()) ? $lstAddresses->First()->address_2 : "",
            !empty($lstAddresses->First()) ? $lstAddresses->First()->city : "",
            !empty($lstAddresses->First()) ? $lstAddresses->First()->state : "",
            !empty($lstAddresses->First()) ? $lstAddresses->First()->zip : "",
            !empty($lstAddresses->First()) ? $lstAddresses->First()->country : "",
            $objCard->card_data->style->card->color->main_rgb->red ?? "255",
            $objCard->card_data->style->card->color->main_rgb->green ?? "00",
            $objCard->card_data->style->card->color->main_rgb->blue ?? "00",
            !empty($lstConnections) ? $lstConnections->FindEntityByValue("connection_type_id","Website")->connection_value ?? "" : "",
            !empty($lstConnections) ? $lstConnections->FindEntityByValue("connection_type_id","Facebook")->connection_value ?? "" : "",
            !empty($lstConnections) ? $lstConnections->FindEntityByValue("connection_type_id","Google+")->connection_value ?? "" : "",
            !empty($lstConnections) ? $lstConnections->FindEntityByValue("connection_type_id","Instagram")->connection_value ?? "" : "",
            !empty($lstConnections) ? $lstConnections->FindEntityByValue("connection_type_id","LinkedIn")->connection_value ?? "" : "",
            !empty($lstConnections) ? $lstConnections->FindEntityByValue("connection_type_id","Pinterest")->connection_value ?? "" : "",
            !empty($lstConnections) ? $lstConnections->FindEntityByValue("connection_type_id","Snapchat")->connection_value ?? "" : "",
            !empty($lstConnections) ? $lstConnections->FindEntityByValue("connection_type_id","Twitter")->connection_value ?? "" : "",
            !empty($lstConnections) ? $lstConnections->FindEntityByValue("connection_type_id","Vimeo")->connection_value ?? "" : "",
            !empty($lstConnections) ? $lstConnections->FindEntityByValue("connection_type_id","YouTube")->connection_value ?? "" : "",
            !empty($lstConnections) ? $lstConnections->FindEntityByValue("connection_type_id","Bandcamp")->connection_value ?? "" : "",
            !empty($lstConnections) ? $lstConnections->FindEntityByValue("connection_type_id","SoundCloud")->connection_value ?? "" : "",
            !empty($lstConnections) ? $lstConnections->FindEntityByValue("connection_type_id","Twitch")->connection_value ?? "" : "",
            $objOwner->username,
        );

        $strContent = str_replace($objCarotData, $objCarotReplace, $strContent);

        return $strContent;
    }

    public function LoadTabClasses() : array
    {
        $objTabFilesDir = glob(PublicData . "_ez/tabs/v1/controllers/*Controller.php");
        $arTabClasses = array();

        foreach( $objTabFilesDir as $currTabFileDir)
        {
            if ( is_file($currTabFileDir))
            {
                $classes = get_declared_classes();
                include_once $currTabFileDir;
                $diff = array_diff(get_declared_classes(), $classes);
                $arTabClasses[] = reset($diff);
            }
        }

        return $arTabClasses;
    }

    public function GetTabClassProperties($strTabClassName) : ExcellTransaction
    {
        $objResult = new ExcellTransaction();

        //$strTabClass = new $strTabClassName

        if (!is_a($strTabClassName, AppController::class))
        {
            $arTabClasses = CardPage::LoadTabClasses();

            if (!in_array($strTabClassName, $arTabClasses))
            {
                $objResult->Result->Success = false;
                $objResult->Result->Count = 0;
                return $objResult;
            }
        }

        $objTabClass = new $strTabClassName();
        $objTabClassProperties = get_object_vars($objTabClass);

        $arTabClassProperties = [];

        foreach($objTabClassProperties as $currPropertyName => $currPropertyValue)
        {
            if (strtolower($currPropertyName) === "description")
            {
                continue;
            }

            if (!is_array($currPropertyValue))
            {
                if (is_bool($currPropertyValue))
                {
                    $arTabClassProperties[] = [
                        "name" => $currPropertyName,
                        "label" => ucwordsToSentences($currPropertyName),
                        "default" => $currPropertyValue ? 'True' : 'False',
                        "type" => "radio",
                        "options" => ["True","False"],
                    ];
                }
                else
                {
                    $arTabClassProperties[] = [
                        "name" => $currPropertyName,
                        "label" => ucwordsToSentences($currPropertyName),
                        "default" => $currPropertyValue,
                        "type" => "text",
                        "max" => 255,
                    ];
                }
            }
            else
            {
                // loop through array.
            }
        }

        $objResult->Result->Success = true;
        $objResult->Result->Count = count($arTabClassProperties);
        $objResult->Data = $arTabClassProperties;

        return $objResult;
    }

    public function GetTabInstanceClassProperties($arTabClassProperties, $objCardPageProperties = null) : ExcellTransaction
    {
        $objResult = new ExcellTransaction();

        if (!empty($arTabClassProperties) && is_iterable($arTabClassProperties) && !empty($objCardPageProperties) && is_iterable($objCardPageProperties))
        {
            foreach($objCardPageProperties as $currPropertiyName => $currPropertyValue)
            {
                foreach($arTabClassProperties as $currIndex => $currTabClassProperty)
                {
                    if ($currTabClassProperty["name"] == $currPropertiyName && !empty($currPropertyValue))
                    {
                        $arTabClassProperties[$currIndex]["default"] = $currPropertyValue;
                    }
                }
            }
        }

        $objResult->Result->Success = true;
        $objResult->Result->Count = count($arTabClassProperties);
        $objResult->Data = $arTabClassProperties;

        return $objResult;
    }

    public function BuildTabClassPropertiesFields($arTabClassProperties) : string
    {
        $strHtmlProperties = "";

        $objTabClassProperties = (object) $arTabClassProperties;

        foreach($objTabClassProperties as $currIndex => $currTabClassProperty)
        {

        }

        return $strHtmlProperties;
    }
}

