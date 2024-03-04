<?php

namespace Entities\Users\Classes\Factories;

use App\Core\Abstracts\AbstractFactory;
use App\Core\App;
use App\Utilities\Database;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Users\Classes\Connections;
use Entities\Users\Classes\Users;
use Entities\Users\Models\ConnectionModel;
use Entities\Users\Models\UserModel;

class UserFactory extends AbstractFactory
{
    private App $app;
    private Users $users;
    private Connections $connections;

    public function __construct(App $app, Users $users, Connections $connections)
    {
        $this->app = $app;
        $this->users = $users;
        $this->connections = $connections;
    }

    public function createUserFromData(string $firstName, string $lastName, string $email, string $phone, string $userName, string $password, string $affiliateId = null) : ExcellTransaction
    {
        $customPlatformId = $this->app->objCustomPlatform->getCompany()->company_id;
        $userQuery = "SELECT ur.* FROM excell_main.user ur LEFT JOIN excell_main.connection con ON con.connection_id = ur.user_email WHERE company_id = {$customPlatformId} (con.connection_value = '" . $email . "' OR ur.username = '" . $userName . "');";

        $objDirectoryResult = Database::getSimple($userQuery);

        if ($objDirectoryResult->result->Count > 0) {
            return $this->processReturn(false, ["error" => "duplicate_account"], "Duplicate user account.");
        }

        $strPrimaryEmail = $email ?? null;
        $strPrimaryPhone = $phone ?? null;

        $blnEmailMatch = $this->users->findMatchingPrimaryEmail($strPrimaryEmail, $this->app->objCustomPlatform->getCompanyId());
        $blnPhoneMatch = $this->users->findMatchingPrimaryPhone($strPrimaryPhone, $this->app->objCustomPlatform->getCompanyId());

        if ($blnEmailMatch->data["match"] === true) {
            return $this->processReturn(false, ["error" => "primary_email_exists"], "Primary E-mail already assigned to another user.");
        }

        if ($blnPhoneMatch->data["match"] === true ) {
            return $this->processReturn(false, ["error" => "primary_phone_exists"], "Primary Phone already assigned to another user..");
        }

        $objUser = new UserModel();
        $objUser->first_name = $firstName;
        $objUser->last_name = $lastName;
        $objUser->username = $userName;
        $objUser->password = $password;
        $objUser->company_id = $customPlatformId;
        $objUser->division_id = 0;
        $objUser->sponsor_id = (!empty($affiliateId) ? $affiliateId : $this->app->objCustomPlatform->getCompany()->default_sponsor_id);

        $objNewUserResult = $this->users->createNew($objUser);

        if ($objNewUserResult->result->Success === false) {
            return $this->processReturn(false, ["error" => "creation_failed"], $objNewUserResult->result->Message);
        }

        $objNewUser = $objNewUserResult->getData()->first();

        if ($strPrimaryEmail !== null) {
            $objConnection = new ConnectionModel();

            $objConnection->user_id = $objNewUserResult->getData()->first()->user_id;
            $objConnection->connection_type_id = 6;
            $objConnection->division_id = 0;
            $objConnection->company_id = $customPlatformId;
            $objConnection->connection_value = $strPrimaryEmail;
            $objConnection->is_primary = EXCELL_TRUE;
            $objConnection->connection_class = 'user';

            $objEmailResult = $this->connections->createNew($objConnection);
            $objNewUser->user_email = $objEmailResult->getData()->first()->connection_id;
        }

        if ($strPrimaryPhone !== null) {
            $objConnection = new ConnectionModel();

            $objConnection->user_id = $objNewUserResult->getData()->first()->user_id;
            $objConnection->connection_type_id = 1;
            $objConnection->division_id = 0;
            $objConnection->company_id = $customPlatformId;
            $objConnection->connection_value = $strPrimaryPhone;
            $objConnection->is_primary = EXCELL_TRUE;
            $objConnection->connection_class = 'user';

            $objPhoneResult = $this->connections->createNew($objConnection);
            $objNewUser->user_phone = $objPhoneResult->getData()->first()->connection_id;
        }

        return $this->users->update($objNewUser);
    }

    public function updateUserFromDataIfMatch(int $userId, string $firstName, string $lastName, string $email, string $phone, string $userName, string $status, string $password = null, string $affiliateId = null) : ExcellTransaction
    {
        $userResult = $this->users->getById($userId);

        if ($userResult->result->Count !== 1) {
            return $this->processReturn(false, ["error" => "no_user_found"], "User not found");
        }

        $objUser = $userResult->getData()->first();

        return $this->updateUserModel($objUser, $firstName, $lastName, $email, $phone, $userName, $status, $password, $affiliateId);
    }

    public function updateUserModel(UserModel $user, string $firstName, string $lastName, string $email, string $phone, string $userName, string $status, string $password = null, string $affiliateId = null) : ExcellTransaction
    {
        $customPlatformId = $this->app->objCustomPlatform->getCompany()->company_id;
        $user->first_name   = $firstName;
        $user->last_name    = $lastName;
        $user->username     = $userName;
        $user->status       = $status;

        if (!empty($objPost->password)) {
            $user->password = $objPost->password;
        }

        $user->company_id = $customPlatformId;
        $user->division_id = 0;
        $user->sponsor_id = (!empty($affiliateId) ? $affiliateId : $this->app->objCustomPlatform->getCompany()->default_sponsor_id);

        $strPrimaryEmail = $email ?? null;
        $strPrimaryPhone = $phone ?? null;

        $blnEmailMatch = $this->users->findMatchingUserEmail($strPrimaryEmail, $user->user_id);
        $blnPhoneMatch = $this->users->findMatchingUserPhone($strPrimaryPhone, $user->user_id);

        if ($blnEmailMatch->data["match"] === true) {
            $user->user_email = $blnEmailMatch->data["entity"]->connection_id;
        } else {
            $objConnection = new ConnectionModel();

            $objConnection->user_id = $user->user_id;
            $objConnection->connection_type_id = 6;
            $objConnection->division_id = 0;
            $objConnection->company_id = $customPlatformId;
            $objConnection->connection_value = $strPrimaryEmail;
            $objConnection->is_primary = EXCELL_TRUE;
            $objConnection->connection_class = 'user';

            $objEmailResult = $this->connections->createNew($objConnection);
            $user->user_email = $objEmailResult->getData()->first()->connection_id;
        }

        if ($blnPhoneMatch->data["match"] === true) {
            $user->user_phone = $blnPhoneMatch->data["entity"]->connection_id;
        } else {
            $objConnection = new ConnectionModel();

            $objConnection->user_id = $user->user_id;
            $objConnection->connection_type_id = 1;
            $objConnection->division_id = 0;
            $objConnection->company_id = $customPlatformId;
            $objConnection->connection_value = $strPrimaryPhone;
            $objConnection->is_primary = EXCELL_TRUE;
            $objConnection->connection_class = 'user';

            $objPhoneResult = $this->connections->createNew($objConnection);
            $user->user_phone = $objPhoneResult->getData()->first()->connection_id;
        }

        return $this->users->update($user);
    }

    public function processIntegrationsUpdate(UserModel $user) : void
    {
        $query = "UPDATE excell_integrations.integrations_user 
            SET synced = 0
            WHERE user_id = {$user->user_id};";

        global $app;
        Database::SetDbConnection($app->objDBs->Integration)::update($query);
    }

    public function processIntegrationsCreate(UserModel $user) : void
    {
        $syncType = "1";
        $currentTimestamp  = date("Y-m-d H:i:s");
        $state  = "create";

        $query = "INSERT INTO excell_integrations.integrations_user 
            (integration_type, user_id, synced, created_on, state)
        VALUES 
            ($syncType, {$user->user_id}, 0, '$currentTimestamp', '$state');";

        global $app;
        Database::SetDbConnection($app->objDBs->Integration)::update($query);
    }

    public function renderUserReturnArray(UserModel $user, string $email, string $phone) : array
    {
        $arUser = $user->ToPublicArray(["sys_row_id", "user_id", "first_name", "last_name", "user_email", "user_phone", "username"]);
        $arUser["id"] = $arUser["sys_row_id"];
        $arUser["email"] = $email;
        $arUser["phone"] = $phone;
        unset($arUser["sys_row_id"]);

        return $arUser;
    }
}