<?php

namespace Entities\Users\Classes;

use App\Core\App;
use App\Core\AppEntity;
use App\Utilities\Database;
use App\Utilities\Excell\ExcellRelationship;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Activities\Classes\UserLogs;
use Entities\Cards\Classes\Cards;
use Entities\Cards\Classes\CardRels;
use Entities\Cards\Models\CardModel;
use Entities\Users\Models\ConnectionModel;
use Entities\Users\Models\UserClassModel;
use Entities\Users\Models\UserModel;
use Entities\Visitors\Classes\VisitorBrowser;
use Entities\Visitors\Models\VisitorBrowserModel;

class Users extends AppEntity
{
    public string $strEntityName       = "Users";
    public $strAliasName        = "users";
    public $strDatabaseTable    = "user";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = UserModel::class;
    public $strMainModelPrimary = "user_id";
    public $intDefaultSponsor   = 726;
    public $isPrimaryModule     = true;

    public function browserRelationship() : ExcellRelationship
    {
        return $this->buildRelationshipModel("browser", "Traffic", "visitor_browser", "visitor_browser_id", "user_id", "user_id");
    }

    public function AuthenticateUserForLogin(UserModel $objRequest, $log = true) : ExcellTransaction
    {
        $objAuthenticationResult = new ExcellTransaction();

        if ( empty($objRequest->username) || empty($objRequest->password) )
        {
            if ($log) { (new UserLogs())->RegisterActivity(0, "logged_in", "Login Failed: Username: " . $objRequest->username . " Password: " . $objRequest->password, "process"); }

            $objAuthenticationResult->result->Success = false;
            $objAuthenticationResult->result->Count   = 0;
            $objAuthenticationResult->result->Message = "You must include both a username and a password. {$objRequest->username} & {$objRequest->password}";

            return $objAuthenticationResult;
        }

        $strUsername = onlyAlphanumeric($objRequest->username);
        $strPassword = $objRequest->password;
        $companyId = $this->app->objCustomPlatform->getCompanyId();

        $objWhereClause = "
            SELECT ur.user_id, ur.username, ur.password
            FROM `excell_main`.`user` ur
            LEFT JOIN `excell_main`.`user_class` uc ON uc.user_id = ur.user_id AND (uc.user_class_type_id >= 0 AND uc.user_class_type_id <= 3)
            WHERE (ur.username = '{$strUsername}' AND ur.company_id = '{$companyId}') OR (ur.username = '{$strUsername}' AND (uc.user_class_type_id >= 0 AND uc.user_class_type_id <= 3))";

        $userResult = Database::getSimple($objWhereClause, "user_id");
        $userResult->getData()->HydrateModelData(UserModel::class, true);

        if ($userResult->result->Success === false || $userResult->result->Count === 0)
        {
            if ($log) { (new UserLogs())->RegisterActivity(0, "logged_in", "Login Failed: Username not found: " . $objRequest->username, "process"); }

            $objAuthenticationResult->result->Success = false;
            $objAuthenticationResult->result->Count = 0;
            $objAuthenticationResult->result->Message = "Your credentials were incorrect: " . $userResult->result->Message;
            $objAuthenticationResult->result->Query = $objWhereClause;

            return $objAuthenticationResult;
        }

        $objUser = $userResult->getData()->first();

        if (passwordCheck($strPassword, $objUser->password) !== true)
        {
            if ($log) { (new UserLogs())->RegisterActivity(0, "logged_in", "Login Failed: Password for {$objRequest->username} not correct: " . $objRequest->password, "process"); }

            $objAuthenticationResult->result->Success = false;
            $objAuthenticationResult->result->Count = 0;
            $objAuthenticationResult->result->Message = "Your credentials were incorrect.";

            return $objAuthenticationResult;
        }

        $objUserResult = $this->getFks()->getById($objUser->user_id);
        $objUser = $objUserResult->getData()->first();

        $objAuthenticationResult->result->Success = true;
        $objAuthenticationResult->result->Count = 1;
        $objAuthenticationResult->result->Message = "We found a matching user!";
        $objAuthenticationResult->getData()->Add($objUser);

        return $objAuthenticationResult;
    }

    public function setUserLoginCookies(UserModel &$objUser, &$app = null) : void
    {
        if ($app === null)
        {
            $app = $this->app;
        }

        setcookie('username', $objUser->username, strtotime('+1 years'), '/', $app->rootDomain, $app->objSslSecure, false) or die("unable to create cookie 3");
        setcookie('userNum', $objUser->user_id, strtotime('+1 years'), '/', $app->rootDomain, $app->objSslSecure, false) or die("unable to create cookie 3");
        setcookie('userId', $objUser->sys_row_id, strtotime('+1 years'), '/', $app->rootDomain, $app->objSslSecure, false) or die("unable to create cookie 3");
    }

    public function setUserActiveCookies($intRandomId, &$app = null) : void
    {
        if ($app === null)
        {
            $app = $this->app;
        }

        setcookie('activeLogin', $intRandomId, strtotime('+1 years'), '/', $app->rootDomain, $app->objSslSecure, false) or die("unable to create cookie 2");
    }

    public function setUserLoginSessionData(UserModel &$objUser, $browserId, &$app = null) : ?int
    {
        /* Set cookie to last 1 year */
        $intRandomId = rand(1000, 9999);

        if ($app === null)
        {
            $app = $this->app;
        }

        $strBrowserCookie = $_COOKIE['instance'];
        $objNewBrowserCookie = new VisitorBrowserModel();
        $objBrowserCookieResult = (new VisitorBrowser())->getWhere(["browser_cookie" => $strBrowserCookie]);

        if ($objBrowserCookieResult->result->Count === 0)
        {
            logText("BrowserCookieLoginAttempt.log", $strBrowserCookie);
            $objNewBrowserCookie->browser_cookie = $strBrowserCookie;
            $objNewBrowserCookie->contact_id = 1337;
            $objNewBrowserCookie->created_on = date("Y-m-d H:i:s");
            $result = (new VisitorBrowser())->createNew($objNewBrowserCookie);
        }
        else
        {
            $objNewBrowserCookie = $objBrowserCookieResult->getData()->first();
        }

        $objNewBrowserCookie->user_id = $objUser->user_id;
        $objNewBrowserCookie->logged_in_at = date("Y-m-d H:i:s");

        (new VisitorBrowser())->update($objNewBrowserCookie);

        if (!empty($app->objAppSession["Core"]["Account"]["Active"]) && is_array($app->objAppSession["Core"]["Account"]["Active"]))
        {
            foreach ( $app->objAppSession["Core"]["Account"]["Active"] as $objRegisteredLogins )
            {
                if ($objRegisteredLogins["user_id"] === $objUser->user_id)
                {
                    $app->objAppSession["Core"]["Account"]["Primary"] = $objUser->user_id;
                    $app->objAppSession["Core"]["Account"]["PrimaryCount"] += $app->objAppSession["Core"]["Account"]["Primary"] ?? 1;
                    $app->intActiveUserId = (int) $objUser->user_id;
                    return null;
                }
            }
        }

        $app->objAppSession["Core"]["Account"]["Active"][$intRandomId] = array("user_id" => $objUser->user_id, "preferred_name" => $objUser->preferred_name, "username" => $objUser->username, "start_time" => date("Y-m-d h:i:s", strtotime("now")));
        $app->objAppSession["Core"]["Account"]["Primary"] = $objUser->user_id;

        $objUser->last_login = date("Y-m-d H:i:s");

        $objUpdatedUserResult = $this->update($objUser);

        $objUser->loadRoles();
        $objUser->loadDepartments();

        (new UserLogs())->RegisterActivity($objUser->user_id, "logged_in", "Login Successful", "process");

        return $intRandomId;
    }

    public function createNew($objEntityData, $arUserClassType = array()) : ExcellTransaction
    {
        $objEntityData->created_on = date("Y-m-d\TH:i:s");
        $objEntityData->last_updated = date("Y-m-d\TH:i:s");

        $objNewUserResult = parent::createNew($objEntityData);

        if ( $objNewUserResult->result->Success === false)
        {
            return $objNewUserResult;
        }

        $objNewUser = $objNewUserResult->getData()->first();

        $objNewUser->password = encryptPassword($objNewUser->password);

        $objNewUser = $this->update($objNewUser);

        if (!empty($arUserClassType) && is_array($arUserClassType) && count($arUserClassType) > 0)
        {
            $objUserClassTypeResult = $this->GetUserClassTypes();
            $objUserClassTypes = $objUserClassTypeResult->getData();

            foreach($objUserClassTypes as $currRowId => $currUserClass)
            {
                foreach($arUserClassType as $currUserClassType)
                {
                    if ( $currUserClass->user_class_type_id === $currUserClassType || $currUserClass->name === $currUserClassType)
                    {
                        $this->CreateNewUserClass($objNewUser->{$this->strMainModelPrimary}, $currRowId);
                    }
                }
            }
        }

        // Create relationship and other stuff!

        return $objNewUserResult;
    }

    public function getByEmailOrUserName(string $email, string $username) : ExcellTransaction
    {
        $objWhereClause = "
            SELECT ur.* FROM user ur
            LEFT JOIN connection cs ON cs.connection_id = ur.user_email
            LEFT JOIN user_class urc ON urc.user_id = ur.user_id
            WHERE ((cs.connection_value = '{$email}' OR ur.username = '{$username}') AND urc.user_class_type_id >= 5 AND ur.company_id = {$this->app->objCustomPlatform->getCompanyId()})
                OR ((cs.connection_value = '{$email}' OR ur.username = '{$username}') AND urc.user_class_type_id <= 4) LIMIT 1";

        $userResult = Database::getSimple($objWhereClause, "user_id");
        $userResult->getData()->HydrateModelData(UserModel::class, true);

        return $userResult;
    }

    public function getByEmail($email) : ExcellTransaction
    {
        $objWhereClause = "
            SELECT ur.* FROM user ur
            LEFT JOIN connection cs ON cs.connection_id = ur.user_email
            LEFT JOIN user_class urc ON urc.user_id = ur.user_id
            WHERE (cs.connection_value = '{$email}' AND urc.user_class_type_id >= 5 AND ur.company_id = {$this->app->objCustomPlatform->getCompanyId()}) 
                OR (cs.connection_value = '{$email}' AND urc.user_class_type_id <= 4) LIMIT 1";

        $userResult = Database::getSimple($objWhereClause, "user_id");
        $userResult->getData()->HydrateModelData(UserModel::class, true);

        return $userResult;
    }

    public function getByPhone($phone) : ExcellTransaction
    {
        $objWhereClause = "
            SELECT ur.* FROM user ur
            LEFT JOIN connection cs ON cs.connection_id = ur.user_phone
            WHERE cs.connection_value = '{$phone}' && ur.company_id = {$this->app->objCustomPlatform->getCompanyId()} LIMIT 1";

        $userResult = Database::getSimple($objWhereClause, "user_id");
        $userResult->getData()->HydrateModelData(UserModel::class, true);

        return $userResult;
    }

    public function CreateNewUserClass($intEntityNum, $intEntityClassNum)
    {
        $objUserClass = new UserClassModel();

        $objUserClass->user_id = $intEntityNum;
        $objUserClass->user_class_type_id = $intEntityClassNum;

        return (new UserClass())->createNew($objUserClass);
    }

    public function GetUserClassesByUserId($intUserId)
    {
        $objClassResult = new ExcellTransaction();

        if (!isInteger($intUserId))
        {
            $objClassResult->result->Success = false;
            $objClassResult->result->Count = 0;
            $objClassResult->result->Message = "The " . $this->strEntityName . " id passed into this class request method must be an integer.";
            $objClassResult->result->Trace = trace();
            return $objClassResult;
        }

        return (new UserClassTypes())->getWhere("user_id", "=", $intUserId);
    }

    public function GetUsersByCardGroupId($intGroupId)
    {
        $objClassResult = new ExcellTransaction();

        if (!isInteger($intGroupId))
        {
            $objClassResult->result->Success = false;
            $objClassResult->result->Count = 0;
            $objClassResult->result->Message = "The " . $this->strEntityName . " id passed into this class request method must be an integer.";
            $objClassResult->result->Trace = trace();
            return $objClassResult;
        }

        $strUserClassQuery = "SELECT * FROM card_rel WHERE card_rel_group_id = $intGroupId;";

        $objCardRel = Database::getSimple($strUserClassQuery,"card_rel_id");

        $objUserWhereclause = array();

        foreach($objCardRel->data as $currCardRelId => $objCardRel)
        {
            $objUserWhereclause[] = ["user_id", "=", $objCardRel->user_id];
            $objUserWhereclause[] = ["OR"];
        }

        array_pop($objUserWhereclause);

        $objUsers = $this->getWhere($objUserWhereclause);

        return $objUsers;
    }

    public function update($objEntityData) : ExcellTransaction
    {
        if (!empty($objEntityData->password) && $objEntityData->password !== EXCELL_EMPTY_STRING && $objEntityData->password !== EXCELL_NULL && strlen($objEntityData->password) < 55)
        {
            $objEntityData->password = encryptPassword($objEntityData->password);
        }

        if ($objEntityData->password === "" || $objEntityData->password === EXCELL_EMPTY_STRING || $objEntityData->password === EXCELL_NULL)
        {
            $objEntityData->password = null;
        }

        return parent::update($objEntityData);
    }

    public function getByUuid($uuid) : ExcellTransaction
    {
        $objWhereClause = "
            SELECT user.*,
            (SELECT platform_name FROM `excell_main`.`company` WHERE company.company_id = user.company_id LIMIT 1) AS platform,
            (SELECT url FROM `excell_media`.`image` WHERE image.entity_id = user.user_id AND image.entity_name = 'user' AND image_class = 'user-avatar' ORDER BY image_id DESC LIMIT 1) AS avatar,
            (SELECT connection_value FROM `excell_main`.`connection` cn WHERE cn.connection_id = user.user_phone AND cn.user_id = user.user_id ORDER BY cn.connection_id DESC LIMIT 1) AS user_phone_value,
            (SELECT connection_value FROM `excell_main`.`connection` cn WHERE cn.connection_id = user.user_email AND cn.user_id = user.user_id ORDER BY cn.connection_id DESC LIMIT 1) AS user_email_value,
            (SELECT COUNT(*) FROM `excell_main`.`card` cd WHERE cd.owner_id = user.user_id) AS cards
            FROM `user` ";

        $objWhereClause .= "WHERE user.sys_row_id = '".$uuid."'";
        $objWhereClause .= " LIMIT 1";

        $cardResult = Database::getSimple($objWhereClause, "user_id");
        $cardResult->getData()->HydrateModelData(UserModel::class, true);

        if ($cardResult->result->Count !== 1)
        {
            return new ExcellTransaction(false, $cardResult->result->Message, ["errors" => [$cardResult->result->Message]]);
        }

        $userSettings = (new UserSettings())->getByUserId($cardResult->getData()->first()->user_id)->getData();
        $cardResult->getData()->HydrateChildModelData("__settings", ["user_id" => "user_id"], $userSettings, false, ["label" => "value"]);

        return $cardResult;
    }

    public function deleteById($intEntityId) : ExcellTransaction
    {
        $objDeletionResult = new ExcellTransaction();

        if (!isInteger($intEntityId))
        {
            $objDeletionResult->result->Success = false;
            $objDeletionResult->result->Count = 0;
            $objDeletionResult->result->Message = "The id passed into this deletion method must be an integer.";
            $objDeletionResult->result->Trace = trace();
            return $objDeletionResult;
        }

        $strUserClassDeletion = "DELETE FROM `user_class` WHERE user_id = " . $intEntityId . ";";

        $this->init();
        $this->Db->update($strUserClassDeletion);

        return parent::deleteById($intEntityId);
    }

    public function GetAllCustomers($intCount = "all", $offset = 0): ExcellTransaction
    {
        if ($intCount !== "all")
        {
            $objCustomersResult = $this->getWhere([["status", "!=", "ALL"], "AND", ["company_id" => $this->app->objCustomPlatform->getCompanyId()]], "user_id", [$offset, $intCount]);
            return $objCustomersResult;
        }

        $objCustomersResult = $this->getWhere([["status", "!=", "ALL"], "AND", ["company_id" => $this->app->objCustomPlatform->getCompanyId()]],"user_id");

        return $objCustomersResult;
    }

    public function GetAllActiveUsers() : ExcellTransaction
    {
        $objCustomers = $this->GetAllCustomersByClass(1);

        return $objCustomers;
    }

    public function GetAllActiveBrandPartners() : ExcellTransaction
    {
        $objCustomers = $this->GetAllCustomersByClass(2);

        return $objCustomers;
    }

    public function GetAllActiveAffiliates() : ExcellTransaction
    {
        $objCustomers = $this->GetAllCustomersByClass(3);

        return $objCustomers;
    }

    public function GetAllCustomersByClass($intUserClassId = 4) : ExcellTransaction
    {
        $objWhereClause = "
            SELECT u.*
            FROM user_class uc 
            LEFT JOIN user u ON uc.user_id = u.user_id
            WHERE uc.user_class_type_id = {$intUserClassId}";

        $objWhereClause .= " AND u.company_id = {$this->app->objCustomPlatform->getCompanyId()}";
        $objWhereClause .= " ORDER BY u.user_id DESC";

        $objUsers = Database::getSimple($objWhereClause,"user_id");
        $objUsers->getData()->HydrateModelData(UserModel::class);

        return $objUsers;
    }

    public function GetAffiliateByUserId($intUserId) : ExcellTransaction
    {
        $objUserResult = $this->getById($intUserId);

        if ($objUserResult->result->Count === 0)
        {
            return $objUserResult;
        }

        if (empty($objUserResult->getData()->first()->sponsor_id))
        {
            $objTransaction = new ExcellTransaction();
            $objTransaction->result->Success = false;
            $objTransaction->result->Count = 0;
            $objTransaction->result->Message = "No affiliate associated with user.";
            return $objTransaction;
        }

        return $this->getById($objUserResult->getData()->first()->sponsor_id);
    }

    public function GetSponsorById($intSponsorId, $connection)
    {
        $objSponsorResult = array();

        $sql = "select displayName, ownerFname, ownerLname, firstName, lastName, customerLevelDepth, sponsorId, ver, "
            .    "(select amount from planCommission "
            .     "where plan_id = (select planId from customers where id = '$intSponsorId') "
            .     "and commissionOption_id = 1) as amount, "
            .    "(select value from customerConnection "
            .     "where connectionTypeId = (select id from connectionType where name = 'Email') "
            .     "and customerId = '$intSponsorId') as Email "
            . "from customers "
            . "where id = '$intSponsorId'";

        $rs  = mysqli_query($connection, $sql);

        if ( ! $rs )
        {
            $objSponsorResult["Result"]["success"] = false;
            $objSponsorResult["Result"]["message"] = mysqli_error($connection);
            return $objSponsorResult;
        }

        $row_count = mysqli_num_rows($rs);

        if ($row_count == 0)
        {
            // Unable to find the requested SID, use the default
            // Later, we'll store data about this request for troubleshooting purposes
            //-------------------------------------------------------------------------
            $intSponsorId = $this->$intDefaultSponsor;  #primary sponsor ID
            $sql          = "select id, displayName, ownerFname, ownerLname, customerLevelDepth, sponsorId, ver, "
                .    "(select amount from planCommission "
                .     "where plan_id = (select planId from customers where id = '$intSponsorId') "
                .     "and commissionOption_id = 1) as amount, "
                .    "(select value from customerConnection "
                .     "where connectionTypeId = (select id from connectionType where name = 'Email') "
                .     "and customerId = '$intSponsorId') as Email "
                . "from customers "
                . "where id = '$intSponsorId'";

            $rs  = mysqli_query($connection, $sql);
            if ( ! $rs ) {
                $objSponsorResult["Result"]["success"] = false;
                $objSponsorResult["Result"]["message"] = mysqli_error($connection);
                return $objSponsorResult;
            }
        }

        $row_data = mysqli_fetch_assoc($rs);

        $objSponsorData = array();
        $objSponsorData["CustomerLevelDepth"] = $row_data['customerLevelDepth'] + 1;

        // mysqli_close($rs);

        $objSponsorData["id"] = $intSponsorId; //final sid determination

        if ( isset($row_data['firstName']))
        {
            $objSponsorData["SponsorFirstName"] = $row_data['firstName'];
        }

        if ( isset($row_data['lastName']))
        {
            $objSponsorData["SponsorLastName"] = $row_data['lastName'];
        }

        if ( isset($row_data['displayName']))
        {
            $objSponsorData["SponsorDisplayName"] = $row_data['displayName'];
        }

        if ( isset($row_data['amount']))
        {
            $objSponsorData["SponsorPercent"] = $row_data['amount'];
        }

        if ( isset($row_data['Email']))
        {
            $objSponsorData["SponsorEmail"] = $row_data['Email'];
        }

        $objSponsorResult["Result"]["success"] = true;
        $objSponsorResult["Result"]["Sponsor"] = $objSponsorData;
        return $objSponsorResult;
    }

    public function GetSponsorFromSession()
    {
        $sid = 0;

        if ( empty($app->objAppSession["Public"]["CardReferral"]["card_id"]))
        {
            if ( !empty($app->objAppSession["Public"]["BrandPartner"]["user_id"]) )
            {
                $sid = $_SESSION["cart"]["BrandPartner"]["user_id"];
                $app->objAppSession["Public"]["CardReferral"]["card_id"] = $sid;
            }
            else
            {
                $sid = (isset($app->objHttpRequest->Data->Params['sid'])) ? $app->objHttpRequest->Data->Params['sid'] : $this->$intDefaultSponsor; //default to primary sponsor ID
                $app->objAppSession["Public"]["CardReferral"]["card_id"] = $sid;
            }
        }
        else
        {
            $sid = $app->objAppSession["Public"]["CardReferral"]["card_id"];
        }

        return $sid;
    }

    public function GetRequestedSponsor($objGetParameters, $connection)
    {
        if (isset($objGetParameters['sid']))
        {
            $intSid = (float)($objGetParameters['sid']);
            $this->app->objAppSession["Public"]["CardReferral"]["card_id"] = $intSid;

            $objBrandPartners = $this->getWhere(["user_id", "=", $intSid],1);

            if( $objBrandPartners->getData()->Count() > 0 )
            {
                // Get Customers BP ID
                $this->app->objAppSession["Public"]["BrandPartner"]["brandpartner_id"] = $objBrandPartners->getData()->first()["bpId"];
                $this->app->objAppSession["Public"]["BrandPartner"]["user_id"] = $intSid;

                $objBrandPartner = $this->GetSponsorById($intSid, $connection);

                $this->app->objAppSession["Public"]["BrandPartner"]["user_data"] = $objBrandPartners->getData()->first();
                $this->app->objAppSession["Public"]["BrandPartner"]["type"] = "brand-partner";
            }
        }

        return $this->GetSponsorFromSession();
    }

    public function GetUserClassTypes() : ExcellTransaction
    {
        $strUserClassQuery = "SELECT * FROM user_class_type;";

        $lstUserClass = $this->Db->getSimple($strUserClassQuery,"user_class_type_id");

        return $lstUserClass;
    }

    public function GetUserConnectionTypes() : ExcellTransaction
    {
        $strUserConnectionQuery = "SELECT * FROM connection_type;";

        $lstUserConnections = $this->Db->getSimple($strUserConnectionQuery,"connection_type_id");

        return $lstUserConnections;
    }

    public function GetByCardId($intCardId) : ExcellTransaction
    {
        $objUserResult = new ExcellTransaction();

        if (!isInteger($intCardId))
        {
            $objUserResult->result->Success = false;
            $objUserResult->result->Count = 0;
            $objUserResult->result->Message = "The card_id value passed into this user request method must be an integer.";
            $objUserResult->result->Trace = trace();
            return $objUserResult;
        }

        $lstCardResult = (new Cards())->getById($intCardId);

        if ($lstCardResult->result->Success === false || $lstCardResult->result->Count === 0)
        {
            $objUserResult->result->Success = false;
            $objUserResult->result->Count = 0;
            $objUserResult->result->Message = "No card was found with ID of {$intCardId}.";
            $objUserResult->result->Trace = trace();
            return $objUserResult;
        }

        $lstCardRelTypeResult = (new Cards())->GetCardRelTypes();
        $objCardRel = (new CardRels())->getWhere(["card_id" => $intCardId]);
        $objCardOwner = (new Users())->GetCardOwnerByCardId($intCardId);
        $arCardUserId = $objCardRel->getData()->FieldsToArray(["user_id"]);
        $arCardUserId[] = $objCardOwner->getData()->first()->user_id;

        $objUsers = $this->getWhereIn("user_id", $arCardUserId);

        $objUsers->getData()->MergeFields($objCardRel->data,["card_rel_type_id","card_rel_id","status"],["user_id"]);
        $objUsers->getData()->MergeFields($lstCardRelTypeResult->data,["name" => "role","card_rel_permissions"],["card_rel_type_id"]);
        $objCardOwner->getData()->first()->AddUnvalidatedValue("card_rel_type_id", 1);
        $objCardOwner->getData()->first()->AddUnvalidatedValue("card_rel_id", "X");
        $objUsers->getData()->{$objCardOwner->getData()->first()->user_id} = $objCardOwner->getData()->first();

        return $objUsers;
    }

    public function GetCardOwnerByCardId($intCardId) : ExcellTransaction
    {
        $objUserResult = new ExcellTransaction();

        if (!isInteger($intCardId))
        {
            $objUserResult->result->Success = false;
            $objUserResult->result->Count = 0;
            $objUserResult->result->Message = "The card_id value passed into this user request method must be an integer.";
            $objUserResult->result->Trace = trace();
            return $objUserResult;
        }

        $objCardResult = (new Cards())->getById($intCardId);

        if ($objCardResult->result->Success === false)
        {
            $objUserResult->result->Success = false;
            $objUserResult->result->Count = 0;
            $objUserResult->result->Message = "This card does not have an owner attached to it.";
            $objUserResult->result->Trace = trace();
            return $objUserResult;
        }

        $objUsers = $this->getWhere(["user_id" => $objCardResult->getData()->first()->owner_id], 1);

        if ($objUsers->result->Success === false || $objUsers->result->Count === 0)
        {
            return $objUsers;
        }

        $objUsers->getData()->first()->AddUnvalidatedValue("role", "Card Owner");
        $objUsers->getData()->first()->AddUnvalidatedValue("card_rel_type_id", 1);

        return $objUsers;
    }

    public function GetCardAffiliateByCardId($intCardId) : ExcellTransaction
    {
        $objUserResult = new ExcellTransaction();

        if (!isInteger($intCardId))
        {
            $objUserResult->result->Success = false;
            $objUserResult->result->Count = 0;
            $objUserResult->result->Message = "The card_id value passed into this user request method must be an integer.";
            $objUserResult->result->Trace = trace();
            return $objUserResult;
        }

        $strCardRelQuery = "SELECT * FROM card_rel " .
            "WHERE card_id = $intCardId && card_rel_type_id = 1";

        $objCardRelResult = $this->Db->getSimple($strCardRelQuery, "card_rel_id");

        if ($objCardRelResult->result->Success === false)
        {
            $objUserResult->result->Success = false;
            $objUserResult->result->Count = 0;
            $objUserResult->result->Message = "This card does not have an owner attached to it.";
            $objUserResult->result->Trace = trace();
            return $objUserResult;
        }

        $objUsers = $this->getWhere(["user_id" =>$objCardRelResult->getData()->first()->user_id]);

        return $objUsers;
    }

    public function GetAddressById($intUserAddressId) : ExcellTransaction
    {
        $objAddressResult = new ExcellTransaction();

        if (!isInteger($intUserAddressId))
        {
            $objAddressResult->result->Success = false;
            $objAddressResult->result->Count = 0;
            $objAddressResult->result->Message = "The address_id value passed into this address request method must be an integer.";
            $objAddressResult->result->Trace = trace();
            return $objAddressResult;
        }

        $strUserAddressQuery = "SELECT * FROM user_address WHERE address_id = $intUserAddressId;";

        $lstUserAddress = $this->Db->getSimple($strUserAddressQuery,"address_id");

        return $lstUserAddress;
    }

    public function GetAddressesByUserId($intUserId) : ExcellTransaction
    {
        $objAddressResult = new ExcellTransaction();

        if (!isInteger($intUserId))
        {
            $objAddressResult->result->Success = false;
            $objAddressResult->result->Count = 0;
            $objAddressResult->result->Message = "The " . $this->strEntityName . " id passed into this address request method must be an integer.";
            $objAddressResult->result->Trace = trace();
            return $objAddressResult;
        }

        $strUserAddressQuery = "SELECT * FROM user_address WHERE user_id = $intUserId;";

        if ( $this->blnFksReplace === true)
        {
            $strUserAddressQuery = "SELECT " .
                "address_id, " .
                "(SELECT user.username FROM user WHERE user.user_id = user_address.user_id) AS user_id," .
                "display_name, " .
                "address_1, " .
                "address_2, " .
                "address_3, " .
                "city, " .
                "state, " .
                "zip, " .
                "country, " .
                "phone_number, " .
                "fax_number, " .
                "is_primary, " .
                "sys_row_id " .
                "FROM user_address " .
                "WHERE user_id = $intUserId;";
        }

        $lstUserAddress = $this->Db->getSimple($strUserAddressQuery,"address_id");

        return $lstUserAddress;
    }

    public function GetConnectionById($intUserConnectionId) : ExcellTransaction
    {
        $objConnectionResult = new ExcellTransaction();

        if (!isInteger($intUserConnectionId))
        {
            $objConnectionResult->result->Success = false;
            $objConnectionResult->result->Count = 0;
            $objConnectionResult->result->Message = "The connection_id value passed into this connection request method must be an integer.";
            $objConnectionResult->result->Trace = trace();
            return $objConnectionResult;
        }

        $strUserConnectionQuery = "SELECT * FROM connection WHERE connection_id = $intUserConnectionId;";

        if ( $this->blnFksReplace === true)
        {
            $strUserConnectionQuery = "SELECT " .
                "connection_id, " .
                "(SELECT company.company_name FROM company WHERE company.company_id = connection.company_id) AS company_id," .
                "(SELECT division.division_name FROM division WHERE division.division_id = connection.division_id) AS division_id," .
                "(SELECT user.username FROM user WHERE user.user_id = connection.user_id) AS user_id," .
                "(SELECT connection_type.name FROM connection_type WHERE connection_type.connection_type_id = connection.connection_type) AS connection_type," .
                "connection_value, " .
                "sys_row_id " .
                "FROM connection " .
                "WHERE connection_id = $intUserConnectionId;";
        }

        $lstUserConnection = $this->Db->getSimple($strUserConnectionQuery,"connection_id");

        return $lstUserConnection;
    }

    public function GetConnectionsByUserId($intUserId) : ExcellTransaction
    {
        $objConnectionResult = new ExcellTransaction();

        if (!isInteger($intUserId))
        {
            $objConnectionResult->result->Success = false;
            $objConnectionResult->result->Count = 0;
            $objConnectionResult->result->Message = "The " . $this->strEntityName . " id passed into this connection request method must be an integer.";
            $objConnectionResult->result->Trace = trace();
            return $objConnectionResult;
        }

        $strUserConnectionQuery = "SELECT * FROM connection WHERE user_id = $intUserId;";

        if ( $this->blnFksReplace === true)
        {
            $strUserConnectionQuery = "SELECT " .
                "connection_id, " .
                "(SELECT company.company_name FROM company WHERE company.company_id = connection.company_id) AS company_id," .
                "(SELECT division.division_name FROM division WHERE division.division_id = connection.division_id) AS division_id," .
                "(SELECT user.username FROM user WHERE user.user_id = connection.user_id) AS user_id," .
                "(SELECT connection_type.name FROM connection_type WHERE connection_type.connection_type_id = connection.connection_type_id) AS connection_type_name," .
                "(SELECT connection_type.font_awesome FROM connection_type WHERE connection_type.connection_type_id = connection.connection_type_id) AS connection_type_icon," .
                "connection_value, " .
                "sys_row_id " .
                "FROM connection " .
                "WHERE user_id = $intUserId;";
        }

        $lstUserConnection = $this->Db->getSimple($strUserConnectionQuery,"connection_id");

        return $lstUserConnection;
    }

    public function GetPrimaryBusinessByUserId($intUserId) : ExcellTransaction
    {
        $objBusinessResult = new ExcellTransaction();

        if (!isInteger($intUserId))
        {
            $objBusinessResult->result->Success = false;
            $objBusinessResult->result->Count = 0;
            $objBusinessResult->result->Message = "The " . $this->strEntityName . " id passed into this business request method must be an integer.";
            $objBusinessResult->result->Trace = trace();
            return $objBusinessResult;
        }

        $strBusinessQuery = "SELECT * FROM user_business WHERE user_id = $intUserId;";

        $lstBusiness = $this->Db->getSimple($strBusinessQuery,"business_id");

        return $lstBusiness;
    }

    public function getFullUserById($userId) : ExcellTransaction
    {
        $userResult = $this->getFks()->getById($userId);

        if ($userResult->result->Count !== 1)
        {
            return $userResult;
        }

        $connections = $this->GetConnectionsByUserId($userId);
        $addresses = $this->GetAddressesByUserId($userId);

        $userResult->getData()->first()->AddUnvalidatedValue("connections", $connections->data);
        $userResult->getData()->first()->AddUnvalidatedValue("addresses", $addresses->data);

        return $userResult;
    }

    public function findMatchingPrimaryEmail($email, $companyId, $userId = null) : ExcellTransaction
    {
        if (empty($email)) { return new ExcellTransaction(false, "Error: missing email.", ["match" => "error"]); }

        if (empty($userId) || $userId === "undefined")
        {
            $whereClause = "SELECT ur.user_id, cn.* FROM excell_main.user ur 
                INNER JOIN excell_main.connection cn ON cn.connection_id = ur.user_email
                WHERE cn.connection_value = '".$email."' AND cn.company_id = {$companyId};";
        }
        else
        {
            $whereClause = "SELECT ur.user_id, cn.* FROM excell_main.user ur 
                INNER JOIN excell_main.connection cn ON cn.connection_id = ur.user_email
                WHERE cn.connection_value = '".$email."' AND ur.user_id != ".$userId." AND cn.company_id = {$companyId};";
        }

        $userResult = Database::getSimple($whereClause,"user_id");

        if ($userResult->result->Count === 0)
        {
            return new ExcellTransaction(true, "No match found.", ["match" => false]);
        }

        $userResult->getData()->HydrateModelData(ConnectionModel::class, true);

        return new ExcellTransaction(true, "Match found.", ["match" => true, "entity" => $userResult->getData()->first()]);
    }

    public function findMatchingPrimaryPhone($phone, $companyId, $userId = null) : ExcellTransaction
    {
        if (empty($phone)) { return new ExcellTransaction(false, "Error: missing phone.", ["match" => "error"]); }
        if (!isInteger($phone)) { return new ExcellTransaction(false, "Error: phone not a number.", ["match" => "error"]); }

        if (empty($userId) || $userId === "undefined")
        {
            $whereClause = "SELECT ur.user_id, cn.* FROM excell_main.user ur 
                INNER JOIN excell_main.connection cn ON cn.connection_id = ur.user_phone 
                WHERE cn.connection_value = '".preg_replace("/[^0-9]/","", $phone)."' AND cn.company_id = {$companyId};";
        }
        else
        {
            $whereClause = "SELECT ur.user_id, cn.* FROM excell_main.user ur 
                INNER JOIN excell_main.connection cn ON cn.connection_id = ur.user_phone 
                WHERE cn.connection_value = '".preg_replace("/[^0-9]/","", $phone)."' AND ur.user_id != ".$userId."  AND cn.company_id = {$companyId};";
        }

        $userResult = Database::getSimple($whereClause,"user_id");

        if ($userResult->result->Count === 0)
        {
            return new ExcellTransaction(true, "No match found.", ["match" => false, "query" => $whereClause]);
        }

        $userResult->getData()->HydrateModelData(ConnectionModel::class, true);

        return new ExcellTransaction(true, "Match found.", ["match" => true, "entity" => $userResult->getData()->first(), "query" => $whereClause]);
    }

    public function findMatchingUserEmail($email, $userId = null) : ExcellTransaction
    {
        if (empty($email)) { return new ExcellTransaction(false, "Error: missing email.", ["match" => "error"]); }

        $connectionResult = null;

        if (empty($userId) || $userId === "undefined")
        {
            $connectionResult = (new Connections())->getWhere(["connection_value" => $email]);
        }
        else
        {
            $connectionResult = (new Connections())->getWhere(["connection_value" => $email, "user_id" => $userId]);
        }

        if ($connectionResult->result->Count === 0)
        {
            return new ExcellTransaction(true, "No match found.", ["match" => false]);
        }

        return new ExcellTransaction(true, "Match found.", ["match" => true, "entity" => $connectionResult->getData()->first()]);
    }

    public function findMatchingUserPhone($phone, $userId = null) : ExcellTransaction
    {
        if (empty($phone)) { return new ExcellTransaction(false, "Error: missing phone.", ["match" => "error"]); }
        if (!isInteger($phone)) { return new ExcellTransaction(false, "Error: phone not a number.", ["match" => "error"]); }

        $connectionResult = null;

        if (empty($userId) || $userId === "undefined")
        {
            $connectionResult = (new Connections())->getWhere(["connection_value" => $phone]);
        }
        else
        {
            $connectionResult = (new Connections())->getWhere(["connection_value" => $phone, "user_id" => $userId]);
        }

        if ($connectionResult->result->Count === 0)
        {
            return new ExcellTransaction(true, "No match found.", ["match" => false]);
        }

        return new ExcellTransaction(true, "Match found.", ["match" => true, "entity" => $connectionResult->getData()->first()]);
    }

    public function generatePasswordResetToken() : string
    {
        return getGuid();
    }
}
