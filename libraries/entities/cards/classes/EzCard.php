<?php

namespace Entities\Cards\Classes;

use App\Core\App;
use App\Utilities\Excell\ExcellHttpModel;
use Entities\Cards\Models\CardModel;

class EzCard
{
    public function RenderCardByCardEntity(CardModel $objCard, ExcellHttpModel $objData, App $app) : void
    {
        $objCard->LoadFullCard();

        $intTemplateFolder = $objCard->template_id ?? "1";
        $intUserId = $objCard->owner_id;
        $intCardId = $objCard->card_id;
        $intCardNum = $objCard->card_num;

        $objLoggedInUser = $app->getActiveLoggedInUser();

        $strEzCardTemplatePath = PUBLIC_DATA . "_ez/templates/" . $intTemplateFolder . "/index" . XT;

        $intMainColorRed = $objCard->card_data->style->card->color->main_rgb->red;
        $intMainColorRedDark = darkenColorChannel($intMainColorRed, 50);
        $intMainColorGreen = $objCard->card_data->style->card->color->main_rgb->green;
        $intMainColorGreenDark = darkenColorChannel($intMainColorGreen, 50);
        $intMainColorBlue = $objCard->card_data->style->card->color->main_rgb->blue;
        $intMainColorBlueDark = darkenColorChannel($intMainColorBlue, 50);

        // Define EZcard Width
        $intCardWidth = findFirstInteger($objCard->card_data->style->card->width ?? null, $objCard->Template->data->style->card->width ?? null, 400);

        // Define EZcard Tab Height
        $intCardPageHeight = floor(
            (
                floatval(
                    findFirstInteger($objCard->card_data->style->tab->height ?? null, $objCard->Template->data->style->tab->height ?? null, 55)
                )
                -25
            )
            /2
        );

        // Output buffering start
        ob_start();

        require $strEzCardTemplatePath;

        // Get output buffering results
        $strEzCardHtmlOutput = ob_get_clean();

        die($strEzCardHtmlOutput);
    }
}