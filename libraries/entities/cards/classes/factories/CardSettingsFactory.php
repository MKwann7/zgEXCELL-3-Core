<?php

namespace Entities\Cards\Classes\Factories;

use App\Core\Abstracts\AbstractFactory;
use App\Core\App;
use App\Utilities\Excell\ExcellCollection;
use Entities\Cards\Classes\Cards;
use Entities\Cards\Classes\CardSettings;
use Entities\Cards\Models\CardModel;
use Entities\Cards\Models\CardSettingModel;

class CardSettingsFactory extends AbstractFactory
{
    private App $app;
    private Cards $cards;
    private CardSettings $settings;

    public function __construct(App $app, Cards $cards, CardSettings $settings)
    {
        $this->app = $app;
        $this->cards = $cards;
        $this->settings = $settings;
    }

    public function processSettingsUpsertFromFullCard(CardModel $card, $postData): ExcellCollection
    {
        foreach ($postData as $currKey => $currData) {
            $currSetting = $card->Settings->FindEntityByValue("label", $currKey);
            if ($currData instanceof \stdClass) {
                $currData = json_encode($currData);
            }
            if (empty($currSetting->card_setting_id)) {
                $newSetting = new CardSettingModel(["label" => $currKey, "value" => $this->setValue($currData), "tags" => "card", "card_id" => $card->card_id]);
                $this->settings->createNew($newSetting);
            } else {
                try {
                    $currSetting->value = $this->setValue($currData);
                } catch ( \TypeError $error) {
                    dd($currData);
                }

                $this->settings->update($currSetting);
            }
        }

        return $card->LoadCardSettings(false);
    }

    private function setValue(string $avatar): string {
        if ($avatar === "__remove__" || $avatar === "___REMOVE___") {
            return EXCELL_EMPTY_STRING;
        }
        return $avatar;
    }
}