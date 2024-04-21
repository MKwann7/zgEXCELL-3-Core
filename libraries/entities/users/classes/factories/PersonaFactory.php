<?php

namespace Entities\Users\Classes\Factories;

use App\Core\Abstracts\AbstractFactory;
use App\Core\App;
use App\Utilities\Excell\ExcellCollection;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Cards\Classes\Cards;
use Entities\Cards\Models\CardModel;
use Entities\Cart\Classes\CartProductCapsule;
use Entities\Cart\Classes\Factories\CartProcessOptions;
use Entities\Cart\Classes\Factories\CartPurchaseFactory;
use Entities\Users\Classes\Users;

class PersonaFactory extends AbstractFactory
{
    private App $app;
    private Users $users;
    private Cards $personas;

    public function __construct(App $app, Users $users, Cards $personas)
    {
        $this->app = $app;
        $this->users = $users;
        $this->personas = $personas;
    }

    public function getPersonasForDirectoryRegistration($userId) : ExcellTransaction
    {
        return $this->personas->getWhere(["owner_id" => $userId, "card_type_id" => 2]);
    }

    public function processFreePersonaPurchase($userId, CartPurchaseFactory $purchaseFactory) : ExcellTransaction
    {
        $personaPackage = [];
        $defaultPersonaPackageId = $this->app->getCustomPlatform()->getCompanySettings()->FindEntityByValue("label","default_persona_package_id")->value ?? 14;
        $personaPackage[] = [CartPurchaseFactory::VARIATION_ID_FIELD => $defaultPersonaPackageId, "quantity" => 1];

        $newCartOptions = new CartProcessOptions();
        $newCartOptions->parent_entity_type = "account";

        $purchaseResult = $purchaseFactory->setFreePersonaPurchase()->processShoppingCart($personaPackage, 0, $userId, 0, $newCartOptions);

        if ($purchaseResult->getResult()->Success === false) {
            return $purchaseResult;
        }

        $cardItems = new ExcellCollection();

        /** @var CartProductCapsule $currCartItem */
        foreach ($purchaseFactory->getProductProcessor()->cartItems as $currCartItem)
        {
            $cardResult = (new Cards())->getByUuid($currCartItem->getProductInstantiation()->sys_row_id);

            /** @var CardModel $card */
            $card = $cardResult->getData()->first();

            if ( $card !== null) {
                $card->LoadCardPages(false);
                $card->LoadCardConnections(false);
                $card->LoadCardContacts();

                $cardItems->Add($card);
            }
        }

        return new ExcellTransaction(true, "We found this.", $cardItems);
    }
}