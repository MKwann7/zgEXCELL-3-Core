<?php
/**
 * Created by PhpStorm.
 * User: mzak
 * Date: 8/4/2018
 * Time: 4:02 PM
 */

use Stripe\Customer;

error_reporting(E_ALL);
ini_set('display_errors', 1);

require(dirname(__FILE__) . '/../../../engine/process/sessions/includes/check-for-ezcard-login.php');
require(dirname(__FILE__) . '/../../../modules/customersold/classes/main.class.php');
require(dirname(__FILE__) . '/../../../modules/cards/classes/main.class.php');
require(dirname(__FILE__) . '/../../../modules/users/classes/UsersModule.php');

if ( $blnUserLoggedIn != true )
{
    die('{"success":false,"message":"You are not authorized to access this. '.json_encode(($_SESSION)).'"}');
}

if ( strtolower($_SERVER['REQUEST_METHOD']) != "post" )
{
    //die("How did you get here?");
}

ini_set('memory_limit', '-1');

$intUpdateCount = 0;

$lstUserRel = array();
$lstBps = (new Customer())->GetBps($connection);

foreach( $lstBps["Result"]["Card"] as $intBpId => $currBpData )
{
    $lstUserRel[] = array(
        "UserId" => $currBpData["id"],
        "Type" => "BrandPartner",
        "Package" => $currBpData["plan_id"],
    );

    // Insert CustomerRel
}

//echo '<pre>';
//print_r($lstUserRel);
//echo '</pre>';
//die;

$lstCustomers = (new Customer())->GetCustomers($connection);

$intUpdateCount = $lstCustomers["Result"]["count"];

// DELETE FROM `card`;
$strCardDeletionQuery  = "DELETE FROM `card`";
$strCardDeletionResultRaw = mysqli_query($connectionV2, $strCardDeletionQuery);

$lstUser = array();
$lstCards = array();
$lstTabs = array();
$lstTabRels = array();

$lstCardRel = array();
$intSkippedCount = 0;
$intNewUserId = 45721;
$intNewCardId = 17450;
$intNewCardPageId = 11750;
$intNewCardPageRelId = 1;

foreach( $lstCustomers["Result"]["Card"] as $intCardId => $currCardData )
{
    $objCard = array();
    $objUser = array();

    if (empty($currCardData["ownerFname"]) || empty($currCardData["ownerLname"]))
    {
        $intSkippedCount++;
        continue;
    }

    if (empty($currCardData["userFname"]) || empty($currCardData["userLname"]))
    {
        $intSkippedCount++;
        continue;
    }

    $strOwnerUnique = $currCardData["ownerFname"]."_".$currCardData["ownerLname"];

    $strUserUnique = $currCardData["userFname"]."_".$currCardData["userLname"];

    $intCardOriginalId = $currCardData["id"];

    if ( $strOwnerUnique != $strUserUnique )
    {
        // sub-card
        $lstCardRel[$intCardOriginalId]["owner_id"] = $currCardData["ownerId"];
        $lstCardRel[$intCardOriginalId]["user_id"] = $intNewUserId;
        $lstCardRel[$intCardOriginalId]["card_id"] = $intNewCardId;
    }

    if ( empty($lstUser[$strUserUnique]))
    {
        // Add New User Account
        $intNewUserId++;

        $lstUser[$strUserUnique]["user_id"] = $intNewUserId;
        $lstUser[$strUserUnique]["company_id"] = $currCardData["company_id"];
        $lstUser[$strUserUnique]["division_id"] = "0";
        $lstUser[$strUserUnique]["sponsor_id"] = $currCardData["sponsorId"];
        $lstUser[$strUserUnique]["account_type_id"] = "2";
        $lstUser[$strUserUnique]["username"] = $currCardData["username"];
        $lstUser[$strUserUnique]["password"] = $currCardData["password"];
        $lstUser[$strUserUnique]["status_id"] = ""; // Get Translated Update
        $lstUser[$strUserUnique]["first_name"] = $currCardData["firstName"];
        $lstUser[$strUserUnique]["last_name"] = $currCardData["lastName"];
        $lstUser[$strUserUnique]["display_name"] = $currCardData["displayName"];
        $lstUser[$strUserUnique]["parent_user_id"] = $currCardData["parentId"];
    }

    if ( empty($lstCards[$intCardOriginalId]))
    {
        // Add New Card Account
        $intNewCardId++;

        $lstCards[$intCardOriginalId]["card_id"] = $intNewCardId;
        $lstCards[$intCardOriginalId]["card_num"] = $intCardOriginalId;
        $lstCards[$intCardOriginalId]["card_vanity_url"] = "";
        $lstCards[$intCardOriginalId]["user_id"] = $intNewUserId;
        $lstCards[$intCardOriginalId]["division_id"] = "0";
        $lstCards[$intCardOriginalId]["company_id"] = $currCardData["company_id"];
        $lstCards[$intCardOriginalId]["card_type_id"] = "";
        $lstCards[$intCardOriginalId]["status_id"] = ""; // Get Translated Update
        $lstCards[$intCardOriginalId]["sponsor_id"] = $currCardData["sponsorId"];
        $lstCards[$intCardOriginalId]["sponsor_referral_id"] = $currCardData["refrerralSponsorId"];
        $lstCards[$intCardOriginalId]["parent_card_id"] = $currCardData["parentId"];
        $lstCards[$intCardOriginalId]["template_card"] = $currCardData["template_card"];
        $lstCards[$intCardOriginalId]["template_id"] = $currCardData["template_id"];
        $lstCards[$intCardOriginalId]["package_plan_id"] = $currCardData["planId"];
        $lstCards[$intCardOriginalId]["created_on"] = date("Y-m-d H:i:s");
        $lstCards[$intCardOriginalId]["last_updated"] = date("Y-m-d H:i:s");

        if ( empty($lstTabs[$intCardOriginalId]) && !empty($currCardData["Tabs"]) )
        {
            foreach($currCardData["Tabs"] as $currTabIndex => $objTabData)
            {
                // Add New Card Account
                $intNewCardPageId++;
                $intNewCardPageRelId++;
                $intCardPageOriginalId = $objTabData["id"];

                $lstTabs[$intCardPageOriginalId]["card_tab_id"]       = $intNewCardPageId;
                $lstTabs[$intCardPageOriginalId]["user_id"]           = $intNewUserId;
                $lstTabs[$intCardPageOriginalId]["division_id"]       = "0";
                $lstTabs[$intCardPageOriginalId]["company_id"]        = $currCardData["company_id"];
                $lstTabs[$intCardPageOriginalId]["title"]             = $objTabData["title"];
                $lstTabs[$intCardPageOriginalId]["content"]           = base64_encode($objTabData["content"]); // Get Translated Update
                $lstTabs[$intCardPageOriginalId]["order_number"]      = $objTabData["orderNumber"];
                $lstTabs[$intCardPageOriginalId]["url"]               = $objTabData["LinkURL"];
                $lstTabs[$intCardPageOriginalId]["visibility"]        = $objTabData["status"];
                $lstTabs[$intCardPageOriginalId]["template_id"]       = $objTabData["template_id"];
                $lstTabs[$intCardPageOriginalId]["template_type"]     = $objTabData["template_type"];
                $lstTabs[$intCardPageOriginalId]["library_tab"]       = $objTabData["library_tab"];
                $lstTabs[$intCardPageOriginalId]["permanent"]         = $objTabData["permanent"];
                $lstTabs[$intCardPageOriginalId]["created_on"]        = date("Y-m-d H:i:s");
                $lstTabs[$intCardPageOriginalId]["last_updated"]      = date("Y-m-d H:i:s");

                $lstTabRels[$intCardOriginalId]["card_tab_rel_id"]   = $intNewCardPageRelId;
                $lstTabRels[$intCardOriginalId]["card_tab_id"]       = $intNewCardPageId;
                $lstTabRels[$intCardOriginalId]["card_id"]           = $intNewCardId;
                $lstTabRels[$intCardOriginalId]["user_id"]           = $intNewUserId;
                $lstTabRels[$intCardOriginalId]["card_tab_rel_type"] = "default"; // default, mirror
            }
        }
    }
}

foreach($lstUser as $intUserUnique => $currUser)
{
/*    $objUserCreationResult = User::CreateNewUser($lstUser[$strUserUnique],$connectionV2);

    if ( $objUserCreationResult["Result"]["success"] == false )
    {
        echo '{"success":false,"message":"Unable to Create User: ' . $intUserUnique . '."}';
    }*/
}

foreach($lstCards as $intCardOriginalIndex => $currCard)
{
    $lstCardRel = array();



    // check for existing user account
    // if not, create user account
    // save to userList

    // create new users

    /*
     *     $objCardCreationResult = Card::CreateNewCard($lstCards[$intCardOriginalId],$connectionV2);

    // create new card.

    if ( $objCardCreationResult["Result"]["success"] == false )
    {
        echo '{"success":false,"message":"Unable to Create User: ' . $intCardOriginalId . '."}';
    }
    */

    //     $lstUser[$strUserUnique]["sponsor_referral_id"] = $currCardData["refrerralSponsorId"];
    //
}

echo '<pre>';
print_r($lstUser);
print_r($lstTabs);
print_r($lstTabRels);
echo '</pre>';

echo '{"success":true,"message":"Users (Count = '.count($lstUser).'). Cards (Count = '.count($lstCards).')."}';
die();