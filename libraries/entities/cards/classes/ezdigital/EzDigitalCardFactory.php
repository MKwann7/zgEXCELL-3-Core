<?php

namespace Entities\Cards\Classes\EzDigital;

use Entities\Cards\Classes\Cards;
use Entities\Cards\Classes\EzCard;
use Entities\Companies\Classes\Companies;

class EzDigitalCardFactory
{
    private $cardFound = false;
    private $httpHeader = null;
    private $accessedByVanityUrl = false;
    private $card = null;
    private $redirectCard = null;
    private $app = null;

    public const CardStatusActive = "active";
    public const CardStatusBuildReady = "build";
    public const CardStatusBuildQa = "buildcomplete";

    public function __construct ($httpHeader, $app)
    {
        $this->httpHeader = $httpHeader;
        $this->app = $app;
    }

    public function process() : bool
    {
        if ($this->httpHeader === null)
        {
            return false;
        }

        $cardResult = (new Cards())->GetByCardNum($this->httpHeader->Uri[0]);

        if ($cardResult->Result->Count === 0)
        {
            $cardResult = (new Cards())->GetByCardVanityUrl($this->httpHeader->Uri[0], $this->app->objCustomPlatform->getCompanyId());

            if ($cardResult->Result === true)
            {
                $this->accessedByVanityUrl = true;
            }
        }

        if ($cardResult->Result->Success === true && $cardResult->Result->Count === 1 && $this->app->isPublicWebsite())
        {
            $this->card = $cardResult->Data->First();
            $this->card->LoadCardImages();
            $this->cardFound = true;
            return true;
        }

        return false;
    }

    public function render($myHub = false) : bool
    {
        if ($this->cardFound === false)
        {
            if ($myHub === true)
            {
                die((new Cards())->getView("card.myhub", $this->app->strAssignedPortalTheme));
            }

            return false;
        }

        $this->redirectCardIfApplicable();

        if ((int)$this->card->company_id !== $this->app->objCustomPlatform->getCompanyId())
        {
            return false;
        }

        switch (strtolower($this->card->status))
        {
            case self::CardStatusActive:
                $this->renderCardWithMyHub();
                break;

            case self::CardStatusBuildReady:
            case self::CardStatusBuildQa:
                die((new Cards())->getView("card.coming_soon", $this->app->strAssignedPortalTheme, ["card" => $this->card]));
        }
        return false;
    }

    private function renderCardWithMyHub() : void
    {
        if ($this->card->template_id > 1 || $this->app->getActiveLoggedInUser()->user_id === 1000)
        {
            die((new Cards())->getView("card.card_base", $this->app->strAssignedPortalTheme, [
                "objCard" => $this->card
            ]));
        }
        else
        {
            (new EzCard())->RenderCardByCardEntity($this->card, $this->httpHeader, $this->app);
        }
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

                if ($objCardRedirectResult->Result->Count === 1)
                {
                    $this->redirectCard = $objCardRedirectResult->Data->First();
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
            $customPlatformResult = (new Companies())->getById($this->redirectCard->company_id)->Data->First();
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