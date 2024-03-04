<?php

namespace Entities\Tasks\Commands;

use App\Utilities\Command\Command;
use App\Utilities\Excell\ExcellCollection;
use App\Utilities\Http\Http;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Cards\Classes\CardPage;
use Entities\Cards\Classes\CardPageRels;
use Entities\Cards\Classes\Cards;
use Entities\Cards\Models\CardModel;
use Entities\Cards\Models\CardPageModel;
use Entities\Cards\Models\CardPageRelModel;
use Entities\Cart\Classes\CartEmails;
use Entities\Cart\Classes\CartProcess;
use Entities\Cart\Classes\CartProductCapsule;
use Entities\Cart\Classes\CartTicketProcess;
use Entities\Cart\Classes\Factories\CartProcessOptions;
use Entities\Cart\Classes\Factories\CartPurchaseFactory;
use Entities\Packages\Models\PackageLineModel;
use Entities\Products\Classes\ProductProcessor;
use Entities\Users\Classes\Connections;
use Entities\Users\Classes\Users;
use Entities\Users\Models\ConnectionModel;
use Entities\Users\Models\UserModel;
use Vtiful\Kernel\Excel;

class ImportUsersAndCardsCommand extends Command
{
    public string $name = "Import.UsersAndCards";

    private int $ezCardCompanyId = 0;
    private int $newMaxCompanyId = 2;
    private int $newMaxDivisionId = 2;

    /**
     * Executes the command
     */
    public function Run(): void
    {
        $userIds = $this->importUserIds();
        $users = $this->importAndInstallUsers($userIds);
        $userCards = $this->importAndInstallCardsByUsers($users);
        $this->installCardAndUserConnections($userCards, $users);

        dd("END OF LINE");
    }

    public function installCardAndUserConnections(ExcellCollection $userCards, ExcellCollection $users): void
    {
        $userCards->Foreach(function(CardModel $cardModel) use ($users) {
            $user = $users->Find(function(UserModel $currUser) use ($cardModel) {
                if ($currUser->user_id = $cardModel->owner_id) {
                    return $currUser;
                }
                return false;
            });
            if (empty($user)) {
                return null;
            }
            dump("Card ID: " . $cardModel->card_id);
            foreach ($cardModel->Connections as $currCardConnection) {
                if (empty($currCardConnection["connection_id"])) {
                    continue;
                }
                $userConnection = $users->Find(function(UserModel $currUser) use ($currCardConnection) {
                    if (empty($currUser->OldConnections)) {
                        return false;
                    }
                    foreach ($currUser->OldConnections as $oldUserConnection) {
                        if (empty($oldUserConnection["connection_id"])) {
                            continue;
                        }
                        if ($oldUserConnection["connection_id"] === $currCardConnection["connection_id"]) {
                            dump( $currUser->first_name . " " .  $currUser->last_name . " had the connection: " . $oldUserConnection["connection_id"]);
                            return $oldUserConnection;
                        }
                    }
                    return false;
                });
                if ($userConnection === null) {
                    dump("We didn't find the user Connection.", $currCardConnection);
                    $newConnection = $this->createNewUserConnection($currCardConnection, $cardModel->owner_id)->getData()->first();

                    if ($currCardConnection["user_id"] !== $cardModel->old_user_id) {
                        dump("We need to update the user connection to ID: " . $cardModel->owner_id);

                        $newConnection = $this->createNewUserConnection($currCardConnection, $cardModel->owner_id)->getData()->first();
                        $userConnections = $cardModel->Connections;

                        foreach($cardModel->Connections as $currConnectionKey => $currUserConnection) {
                            if ($currCardConnection["connection_id"] === $currUserConnection["connection_id"]) {
                                $userConnections[$currConnectionKey]["new_connection_id"] = $newConnection->connection_id;
                            }
                        }

                        $cardModel->AddUnvalid
                        atedValue("Connections", $userConnections);
                    } else {

                    }
                    // create Card Connection Rel...
                } else {
                    dump("We found the user Connection.", $userConnection);
                    if (empty($userConnection["new_connection_id"])) {
                        // sync connection....
                    }
                    // Check to see if this exists, and if not, create it.
                }
            }
            return $cardModel;
        });
    }

    /**
     * @param ExcellCollection $users
     * @return ExcellCollection
     */
    private function importAndInstallCardsByUsers(ExcellCollection $users) : ExcellCollection
    {
        $cardCollection = new ExcellCollection();
        foreach($users as $userData) {
            $cardIds = $this->importCardIdsByUserId($userData->old_user_id);
            foreach($cardIds as $cardId) {
                $card = $this->getCardsByCardId($cardId["card_id"], $userData->user_id, $users);
                if ($card !== null) {
                    echo "Created Card: " . $card->card_id . " - " . $card->old_card_id . PHP_EOL;
                    $cardCollection->Add($card);
                }
            }
        }
        echo "Created " . $cardCollection->Count() . " cards." . PHP_EOL;
        return $cardCollection;
    }

    private function getCardsByCardId(int $cardId, int $userId, ExcellCollection $users): ?CardModel
    {
        $http = new Http();
        $request = $http->get("https://ezcard.com/api/v1/cards/get-card-by-id?id=" . $cardId . "&addons=paymentAccount|paymentHistory");
        $response = $request->send();

        if ($response->statusCode !== 200) {
            return null;
        }

        $cardArray = json_decode($response->body, true);

        if ($cardArray["success"] !== true) {
            return null;
        }

        $cardArray["data"]["card"]["old_card_id"] = $cardArray["data"]["card"]["card_id"];
        $cardArray["data"]["card"]["owner_id"] = $userId;
        $cardArray["data"]["card"]["company_id"] = $this->newMaxCompanyId;
        $cardArray["data"]["card"]["division_id"] = $this->newMaxDivisionId;

        $cardModel = new CardModel($cardArray["data"]["card"], true);
        $cardModel->card_version_id = EXCELL_NULL;
        $cardModel->phone_addon_id = EXCELL_NULL;
        $cardModel->redirect_to = EXCELL_NULL;
        $cardModel->created_by = $userId;
        $cardModel->updated_by = $userId;
        $cardModel->product_id = 1000;

        $cartPurchaseFactory = new CartPurchaseFactory(
            new CartProcess(),
            new ProductProcessor(),
            new CartTicketProcess(),
            new CartEmails(),
            new Cards()
        );

        $newCartOptions = new CartProcessOptions();
        $newCartOptions->company_id = $this->newMaxCompanyId;
        $newCartOptions->division_id = $this->newMaxDivisionId;
        $newCartOptions->default_user_id = 1001;
        $newCartOptions->creation_date_override = $cardModel->created_on;
        $newCartOptions->page_create_count_override = 7;
        $newCartOptions->purchase_price_override = 100;
        $newCartOptions->skip_emails = true;
        $newCartOptions->widgets_for_purchase = new ExcellCollection();

        $packageLine = [
            "package_variation_id" => 4,
            "package_id" => 5,
            "company_id" => 2,
            "division_id" => 2,
            "product_entity" => "product",
            "quantity" => 1,
            "cycle_type" => 5,
            "promo_price" => 0,
            "regular_price" => 0,
            "currency" => "usd",
        ];

        if (!empty($cardModel->Tabs)) {
            foreach ($cardModel->Tabs as $currKey => $currTab) {
                switch((int)$currTab["card_tab_type_id"]) {
                    case 2;
                        if (str_starts_with($currTab["content"], "PHA")) {
                            $cardTabs = $cardModel->Tabs;
                            $cardTabs[$currKey]["card_tab_type_id"] = 1;
                            $cardModel->Tabs = $cardTabs;
                            $newCartOptions->widgets_for_purchase->Add(
                                new PackageLineModel(
                                    array_merge($packageLine, ["product_entity_id" => 1006])
                                )
                            );
                        } elseif (str_starts_with($currTab["content"], "EZdigital Member Directory ")) {
                            $cardTabs = $cardModel->Tabs;
                            $cardTabs[$currKey]["card_tab_type_id"] = 4;
                            $cardModel->Tabs = $cardTabs;
                            $newCartOptions->widgets_for_purchase->Add(
                                new PackageLineModel(
                                    array_merge($packageLine, ["product_entity_id" => 1006])
                                )
                            );
                        } else {
                            switch($currTab["content"]) {
                                case "Tabs_ContactInfoTabController":
                                    $cardTabs = $cardModel->Tabs;
                                    $cardTabs[$currKey]["card_tab_type_id"] = 4;
                                    $cardModel->Tabs = $cardTabs;
                                    $newCartOptions->widgets_for_purchase->Add(
                                        new PackageLineModel(
                                            array_merge($packageLine, ["product_entity_id" => 1003])
                                        )
                                    );
                                    break;
                                case "Tabs_SaveTabController":
                                case "Tabs_ShareOrSaveCardController":
                                $cardTabs = $cardModel->Tabs;
                                $cardTabs[$currKey]["card_tab_type_id"] = 4;
                                $cardModel->Tabs = $cardTabs;
                                    $newCartOptions->widgets_for_purchase->Add(
                                        new PackageLineModel(
                                            array_merge($packageLine, ["product_entity_id" => 1007])
                                        )
                                    );
                                    break;
                                case "EZdigital Member Directory 1.0.1":
                                    $cardTabs = $cardModel->Tabs;
                                    $cardTabs[$currKey]["card_tab_type_id"] = 4;
                                    $cardModel->Tabs = $cardTabs;
                                    $newCartOptions->widgets_for_purchase->Add(
                                        new PackageLineModel(
                                            array_merge($packageLine, ["product_entity_id" => 1006])
                                        )
                                    );
                                    break;
                            }
                        }
                        break;
                    case 4;
                        $newCartOptions->widgets_for_purchase->Add(
                            new PackageLineModel(
                                array_merge($packageLine, ["product_entity_id" => 1006])
                            )
                        );
                        break;
                }
            }
        }

        // 4 is the EZcard Package Variation
        $cardPackage = [
            ["var_id" => 4, "quantity" => 1]
        ];

        $purchaseResult = $cartPurchaseFactory->processShoppingCart($cardPackage, 0, $userId, 0, $newCartOptions);

        if ($purchaseResult->getResult()->Success === false) {
            dump("Shopping Cart Failure", $purchaseResult);
        }

        /** @var CardModel $newCard */
        $newCard = $cartPurchaseFactory->getProductProcessor()->cartItems->Find(function(CartProductCapsule $capsule) {
            if (get_class($capsule->instantiation) === CardModel::class) {
                return $capsule->instantiation;
            }
            return false;
        });

        $newCard->card_vanity_url = $cardModel->card_vanity_url;
        $newCard->card_keyword = $cardModel->card_keyword;
        $newCard->card_num = $cardModel->card_num;
        $newCard->card_data = $cardModel->card_data;
        $newCard->card_name = $cardModel->card_name;

        $cards = new Cards();
        $cardUpdateResults = $cards->update($newCard);

        if (!empty($cardModel->Tabs)) {
            $cardTabs = $cardModel->Tabs;
            $cardTabIndex = 0;
            $cardPages = new CardPage();
            $cardPageRels = new CardPageRels();

            $newCard->cardPages->Foreach(function (\stdClass $page) use ($cardPages, $cardPageRels, $cardTabs, &$cardTabIndex) {
                if (empty($cardTabs[$cardTabIndex] ?? null)) {
                    return $page;
                }
                $cardTab = $cardTabs[$cardTabIndex];
                $cardTabIndex++;

                /** @var CardPageModel $cardPage */
                $cardPage = $page->page;

                if ((int)$cardTab["card_tab_type_id"] === 1) {
                    $cardPage->title = !empty($cardTab["title"]) ? $cardTab["title"] : "";
                    $cardPage->menu_title = !empty($cardTab["title"]) ? $cardTab["title"] : "";
                    $cardPage->url = !empty($cardTab["title"]) ? str_replace("_", "-", strtolower(str_replace(" ", "-", str_replace("  ", "-", $cardTab["title"])))) : "";
                    $cardPage->content = $cardTab["content"];
                    $cardPage->visibility = true;
                    $cardPage->created_on = $cardTab["created_on"];
                    $cardPage->last_updated = $cardTab["last_updated"];
                    $cardPage->library_tab = EXCELL_FALSE;
                    $page->page = $cardPages->update($cardPage)->getData()->first();

                    /** @var CardPageRelModel $cardPageRel */
                    $cardPageRel = $page->pageRel;

                    $cardPageRel->card_tab_rel_title = !empty($cardTab["title"]) ? $cardTab["title"] : "";
                    $cardPageRel->card_tab_rel_menu_title = !empty($cardTab["title"]) ? $cardTab["title"] : "";
                    $cardPageRel->card_tab_rel_url = !empty($cardTab["title"]) ? str_replace("_", "-", strtolower(str_replace(" ", "-", str_replace("  ", "-", $cardTab["title"])))) : "";
                    $cardPageRel->rel_visibility = (bool)$cardTab["rel_visibility"];
                    $cardPageRel->rel_sort_order = $cardTab["rel_sort_order"];
                    $page->pageRel = $cardPageRels->update($cardPageRel)->getData()->first();
                } elseif ((int)$cardTab["card_tab_type_id"] === 2 || (int)$cardTab["card_tab_type_id"] === 4) {
                    $cardPage->title = !empty($cardTab["title"]) ? $cardTab["title"] : "";
                    $cardPage->menu_title = !empty($cardTab["title"]) ? $cardTab["title"] : "";
                    $cardPage->url = !empty($cardTab["title"]) ? str_replace("_", "-", strtolower(str_replace(" ", "-", str_replace("  ", "-", $cardTab["title"])))) : "";
                    $cardPage->content = "";
                    $page->page = $cardPages->update($cardPage)->getData()->first();

                    /** @var CardPageRelModel $cardPageRel */
                    $cardPageRel = $page->pageRel;

                    $cardPageRel->card_tab_rel_title = !empty($cardTab["title"]) ? $cardTab["title"] : "";
                    $cardPageRel->card_tab_rel_menu_title = !empty($cardTab["title"]) ? $cardTab["title"] : "";
                    $cardPageRel->card_tab_rel_url = !empty($cardTab["title"]) ? str_replace("_", "-", strtolower(str_replace(" ", "-", str_replace("  ", "-", $cardTab["title"])))) : "";
                    $cardPageRel->rel_visibility = (bool)$cardTab["rel_visibility"];
                    $cardPageRel->rel_sort_order = $cardTab["rel_sort_order"];
                    $page->pageRel = $cardPageRels->update($cardPageRel)->getData()->first();
                }

                return $page;
            });
        }

        $newCard->AddUnvalidatedValue("Connections", $cardModel->Connections);
        $newCard->AddUnvalidatedValue("Contacts", $cardModel->Contacts);
        $newCard->AddUnvalidatedValue("PaymentAccount", $cardModel->PaymentAccount);

        return $newCard;
    }

    private function importCardIdsByUserId(int $userId) : array
    {
        $http = new Http();

        $request = $http->get("https://ezcard.com/api/v1/cards/get-cards-by-user-id?user_id=" . $userId);
        $response = $request->send();

        if ($response->statusCode !== 200) {
            return [];
        }

        $userData = json_decode($response->body, true);

        if (empty($userData["data"]["cards"])) {
            return [];
        }

        return $userData["data"]["cards"];
    }

    private function importAndInstallUsers(array $userIds) : ExcellCollection
    {
        $userCollection = new ExcellCollection();
        $userCount = 0;
        foreach($userIds as $userData) {
            $user = $this->getUserFromEzcard($userData["user_id"]);
            if ($user !== null) {
                $this->updateUserConnections($user);
                $userCollection->Add($user);
            }
            if ($userCount > 2) {
                break;
            }
            $userCount++;
        }
        echo "Created " . $userCollection->Count() . " users." . PHP_EOL;
        return $userCollection;
    }

    private function getUserFromEzcard(string $userId): ?UserModel
    {
        $http = new Http();
        $request = $http->get("https://ezcard.com/api/v1/users/get-user-by-id?id=" . $userId);
        $response = $request->send();
        if ($response->statusCode !== 200) {
            return null;
        }

        $userArray = json_decode($response->body, true);

        if ($userArray["success"] !== true) {
            return null;
        }

        $userArray["data"]["user"]["old_user_id"] = $userArray["data"]["user"]["user_id"];
        $userArray["data"]["user"]["old_sponsor_id"] = $userArray["data"]["user"]["sponsor_id"];
        unset($userArray["data"]["user"]["user_id"]);
        unset($userArray["data"]["user"]["sponsor_id"]);

        if ($userArray["data"]["user"]["old_sponsor_id"] === 70726) {
            $userArray["data"]["user"]["sponsor_id"] = 1001;
        }

        $userArray["data"]["user"]["company_id"] = $this->newMaxCompanyId;
        $userArray["data"]["user"]["division_id"] = $this->newMaxDivisionId;

        $userModel = new UserModel($userArray["data"]["user"], true);

        $userModel->pin = EXCELL_NULL;
        $userModel->created_by = EXCELL_NULL;
        $userModel->updated_by = EXCELL_NULL;
        $oldUserEmail = $userModel->user_email;
        $oldUserPhone = $userModel->user_phone;
        $userModel->user_email = EXCELL_NULL;
        $userModel->user_phone = EXCELL_NULL;

        if (empty($userModel->last_login) || !strtotime($userModel->last_login) || strtotime($userModel->last_login) < 0) {
            $userModel->last_login = EXCELL_NULL;
        }

        $users = new Users();
        $userTestResult = $users->getWhere(["username" => $userModel->username, "company_id" => $this->newMaxCompanyId]);

        if ($userTestResult->getResult()->Count >= 1) {
            echo "Skipping User: " . $userModel->first_name . " " . $userModel->last_name . PHP_EOL;
            $testUser = $userTestResult->getData()->first();
            $testUser->AddUnvalidatedValue("old_user_email", $oldUserEmail);
            $testUser->AddUnvalidatedValue("old_user_phone", $oldUserPhone);
            $testUser->AddUnvalidatedValue("old_user_id", $userModel->old_user_id);
            $testUser->AddUnvalidatedValue("old_sponsor_id", $userModel->old_sponsor_id);
            $testUser->AddUnvalidatedValue("OldConnections", $userModel->Connections);
            $testUser->AddUnvalidatedValue("Connections", new ExcellCollection());
            return $testUser;
        }

        $userResult = $users->createNew($userModel);

        if ($userResult->getResult()->Success === false) {
            dd("Unable to create new user", $userResult);
        }

        $newUser = $userResult->getData()->first();

        $newUser->AddUnvalidatedValue("old_user_email", $oldUserEmail);
        $newUser->AddUnvalidatedValue("old_user_phone", $oldUserPhone);
        $newUser->AddUnvalidatedValue("old_user_id", $userModel->old_user_id);
        $newUser->AddUnvalidatedValue("old_sponsor_id", $userModel->old_sponsor_id);
        $newUser->AddUnvalidatedValue("OldConnections", $userModel->Connections);
        $newUser->AddUnvalidatedValue("Connections", new ExcellCollection());

        echo "Created User: " . $newUser->first_name . " " . $newUser->last_name . " - " . $newUser->user_id . PHP_EOL;

        return $newUser;
    }

    private function loadExistingUserConnections(int $userId) : ExcellCollection
    {
        $connections = new Connections();
        return $connections->getWhere(["user_id" => $userId])->getData();
    }

    private function createNewUserConnection(array $connection, int $userId) : ExcellTransaction
    {
        $checkConnection = (new Connections())->getWhere(["connection_label" => $connection["connection_id"]]);
        if ($checkConnection->getResult()->Count !== 0) {
            return $checkConnection;
        }
        $objConnection = new ConnectionModel();

        $objConnection->user_id = $userId;
        $objConnection->connection_type_id = $connection["connection_type_id"];
        $objConnection->division_id = $this->newMaxDivisionId;
        $objConnection->company_id = $this->newMaxCompanyId;
        $objConnection->connection_value = $connection["connection_value"];
        $objConnection->is_primary = EXCELL_FALSE;
        $objConnection->connection_class = 'user';

        // We're doing this for storing the old connection id
        $objConnection->connection_label = $connection["connection_id"];

        return (new Connections())->createNew($objConnection);
    }

    private function AssignConnectionData(int $intUserId, string $strConnectionValue, int $connectionType, int $connectionId) : ExcellTransaction
    {
        $checkConnection = (new Connections())->getWhere(["connection_label" => $connectionId]);
        if ($checkConnection->getResult()->Count !== 0) {
            return $checkConnection;
        }

        $objConnection = new ConnectionModel();

        $objConnection->user_id = $intUserId;
        $objConnection->connection_type_id = $connectionType;
        $objConnection->division_id = $this->newMaxDivisionId;
        $objConnection->company_id = $this->newMaxCompanyId;
        $objConnection->connection_value = $strConnectionValue;
        $objConnection->is_primary = EXCELL_TRUE;
        $objConnection->connection_class = 'user';

        // We're doing this for storing the old connection id
        $objConnection->connection_label = $connectionId;

        return (new Connections())->createNew($objConnection);
    }


    private function importUserIds() : array
    {
        $http = new Http();

        $request = $http->get("https://ezcard.com/api/v1/cards/get-active-users?company_id=" . $this->ezCardCompanyId);
        $response = $request->send();

        if ($response->statusCode !== 200) {
            return [];
        }

        $userData = json_decode($response->body, true);

        if (empty($userData["data"]["users"])) {
            return [];
        }

        return $userData["data"]["users"];
    }

    private function updateUserConnections(UserModel &$newUser): void
    {
        if (empty($newUser->OldConnections)) {
            return;
        }

        $userEmail = "";
        $userEmailId = "";
        $userPhone = "";
        $userPhoneId = "";

        foreach ($newUser->OldConnections as $currKey => $currConnection) {
            if ($currConnection["connection_id"] === $newUser->old_user_email) {
                $userEmail = $currConnection["connection_value"];
                $userEmailId = $currConnection["connection_id"];
            }
            if ($currConnection["connection_id"] === $newUser->old_user_phone) {
                $userPhone = $currConnection["connection_value"];
                $userPhoneId = $currConnection["connection_id"];
            }
        }

        if (!empty($userEmail)) {
            $objEmailAddressResult = $this->AssignConnectionData($newUser->user_id, $userEmail,6, $userEmailId);
            $newUser->user_email = $objEmailAddressResult->getData()->first()->connection_id;
            $cardConnections = $newUser->OldConnections;
            foreach($newUser->OldConnections as $currKey => $currUserConnection) {
                if ($userEmailId === $currUserConnection["connection_id"]) {
                    $cardConnections[$currKey]["new_connection_id"] = $objEmailAddressResult->getData()->first()->connection_id;
                }
            }
            $newUser->AddUnvalidatedValue("OldConnections", $cardConnections);
        }

        if (!empty($userPhone)) {
            $objMobileNumberResult = $this->AssignConnectionData($newUser->user_id, $userPhone, 1, $userPhoneId);
            $newUser->user_phone = $objMobileNumberResult->getData()->first()->connection_id;
            $cardConnections = $newUser->OldConnections;
            foreach($newUser->OldConnections as $currKey => $currUserConnection) {
                if ($userPhoneId === $currUserConnection["connection_id"]) {
                    $cardConnections[$currKey]["new_connection_id"] = $objMobileNumberResult->getData()->first()->connection_id;
                }
            }
            $newUser->AddUnvalidatedValue("OldConnections", $cardConnections);
        }

        if (!empty($userEmail) || !empty($userPhone)) {
            (new Users())->update($newUser);
        }

        foreach ($newUser->OldConnections as $currKey => $currConnection) {
            if (empty($currConnection["new_connection_id"])) {
                $newConnection = $this->createNewUserConnection($currConnection, $newUser->user_id)->getData()->first();
                $cardConnections = $newUser->OldConnections;
                foreach($newUser->OldConnections as $currConnectionKey => $currUserConnection) {
                    if ($currConnection["connection_id"] === $currUserConnection["connection_id"]) {
                        $cardConnections[$currConnectionKey]["new_connection_id"] = $newConnection->connection_id;
                    }
                }
                $newUser->AddUnvalidatedValue("OldConnections", $cardConnections);
            }
        }
    }
}