<?php

namespace Entities\Cards\Classes\EzDigital;

use App\Core\App;
use App\Utilities\Excell\ExcellHttpModel;
use Entities\Cards\Classes\Cards;
use Entities\Cards\Classes\EzCard;
use Entities\Cards\Models\CardModel;
use Entities\Companies\Classes\Companies;

class ExcellCardFactory
{

    protected ExcellHttpModel $httpHeader;
    protected App $app;
    protected Cards $cards;

    protected ?CardModel $card = null;
    protected ?CardModel $redirectCard = null;

    protected bool $cardFound = false;
    protected bool $accessedByVanityUrl = false;

    public const CardStatusActive = "active";
    public const CardStatusBuildReady = "build";
    public const CardStatusBuildQa = "buildcomplete";

    public function __construct (App $app, ExcellHttpModel $httpHeader, Cards $cards)
    {
        $this->httpHeader = $httpHeader;
        $this->app = $app;
        $this->cards = $cards;
    }

    protected function getCardByRequest() :?CardModel
    {
        $card = null;

        if ($this->app->getActiveDomain()->getType() !== "app") {
            $card = $this->cards->getById($this->app->getActiveDomain()->getCardId())->getData()->first();
        }

        if ($card === null && !empty($this->httpHeader->Uri[0]) && $this->app->getActiveDomain()->getType() === "app") {
            $card = $this->cards->GetByCardNum($this->httpHeader->Uri[0])->getData()->first();
        }

        return $card;
    }

    public function process() : bool
    {
        $card = $this->getCardByRequest();

        if ($card === null) {
            $card = $this->cards->GetByCardVanityUrl($this->httpHeader->Uri[0], $this->app->objCustomPlatform->getCompanyId())->getData()->first();

            if ($card !== null) {
                $this->accessedByVanityUrl = true;
            }
        }

        if ($card !== null && $this->app->isPublicWebsite()) {
            $this->card = $card;
            $this->card->LoadCardSettings();
            $this->cardFound = true;
            return true;
        }

        return false;
    }

    public function render($myHub = false) : bool
    {
        if ($this->cardFound === false) {
            if ($myHub === true) {
                die((new Cards())->getView("card.myhub", $this->app->strAssignedPortalTheme));
            }

            return false;
        }

        $this->redirectCardIfApplicable();

        if ((int)$this->card->company_id !== $this->app->getCustomPlatform()->getCompanyId()) {
            return false;
        }

        switch (strtolower($this->card->status)) {
            case self::CardStatusActive:
                $this->renderCardWithMyHub();
                break;

            case self::CardStatusBuildReady:
            case self::CardStatusBuildQa:

                $portalTheme = $this->app->getCustomPlatform()->getCompanySettings()->FindEntityByValue("label","portal_theme")->value ?? 1;

                die((new Cards())->getView("card.t".$portalTheme.".coming_soon", $this->app->strAssignedPortalTheme, ["card" => $this->card]));
        }
        return false;
    }

    private function renderCardWithMyHub() : void
    {
        die((new Cards())->getView("card.card_base", $this->app->strAssignedPortalTheme, [
            "objCard" => $this->card
        ]));
    }

    private function redirectCardIfApplicable() : void
    {
        if ($this->cardMustRedirectToCustomPlatform())
        {
            $this->app->redirectToCustomPlatformCard($this->card, $this->accessedByVanityUrl);
        }
        elseif ($this->cardMustRedirectToAnotherCard())
        {
            $this->cardRedirectToAnotherCard();
        }
    }

    private function cardMustRedirectToCustomPlatform() : bool
    {
        if ($this->card->redirect_to !== null && $this->card->redirect_to !== $this->app->objCustomPlatform->getCompanyId())
        {
            if ($this->card->redirect_to <= 1000)
            {
                return true;
            }
        }

        return false;
    }

    private function cardMustRedirectToAnotherCard() : bool
    {
        if ($this->card->redirect_to !== null && $this->card->redirect_to !== $this->app->objCustomPlatform->getCompanyId())
        {
            if ($this->card->redirect_to > 1000)
            {
                $objCardRedirectResult = (new Cards())->GetByCardNum($this->card->redirect_to);

                if ($objCardRedirectResult->result->Count === 1)
                {
                    $this->redirectCard = $objCardRedirectResult->getData()->first();
                    return true;
                }
            }
        }

        return false;
    }

    private function cardRedirectToAnotherCard() : void
    {
        $redirectUrl = getFullUrl();

        if ($this->redirectCard->company_id !== $this->app->objCustomPlatform->getCompanyId())
        {
            $customPlatformResult = (new Companies())->getById($this->redirectCard->company_id)->getData()->first();
            $redirectUrl = "http" . ($customPlatformResult->domain_public_ssl === 1 ? "s://" : "://") . $customPlatformResult->domain_public;
        }

        if (!empty($this->redirectCard->card_vanity_url))
        {
            $this->app->executeUrlRedirect($redirectUrl . "/". $this->redirectCard->card_vanity_url);
        }
        else
        {
            $this->app->executeUrlRedirect($redirectUrl . "/". $this->redirectCard->card_num);
        }
    }
}