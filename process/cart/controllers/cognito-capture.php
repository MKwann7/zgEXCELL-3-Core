<?php

use Entities\Users\Classes\Users;
use Entities\Users\Models\UserModel;

$strCognitoCaptureLog = "CognitoCaptureLog.Process.log";

$details = $app->objHttpRequest->Data->PostData;
$arDetails = (array)$details;

logText($strCognitoCaptureLog,"CognitoFormCapture DATA: " . json_encode($arDetails));

$strPaymentToken              = null;
$strPaymentConfirmationNumber = null;
$strCustomerCardId            = null;
$strCustomerId                = null;
$blnPaymentSuccess            = true;

if (
    empty($details->Entry->PaymentToken->Token) ||
    empty($details->Entry->PaymentConfirmationNumber) ||
    empty($details->Entry->CustomerCard->CustomerCardId) ||
    empty($details->Entry->CustomerCard->CustomerId))
{
    $blnPaymentSuccess = false;
}
else
{
    $strPaymentToken              = $details->Entry->PaymentToken->Token;
    $strPaymentConfirmationNumber = $details->Entry->Order->PaymentConfirmationNumber;
    $strCustomerCardId            = $details->Entry->CustomerCard->CustomerCardId;
    $strCustomerId                = $details->Entry->CustomerCard->CustomerId;
}

$strPaymentToken              = $details->Entry->PaymentToken->Token;
$strPaymentConfirmationNumber = $details->Entry->Order->PaymentConfirmationNumber;
$strCustomerCardId            = $details->Entry->CustomerCard->CustomerCardId;
$strCustomerId                = $details->Entry->CustomerCard->CustomerId;

$strFirstName = $details->Name->First;
$strLastName  = $details->Name->Last;
$strPhone     = $details->OfficePhone;
$strMobile    = $details->Mobile;
$strEmail     = $details->Email;

$strAddress = $details->Entry->Order->BillingAddress->StreetAddress;
$strCity    = $details->Entry->Order->BillingAddress->City;
$strState   = $details->Entry->Order->BillingAddress->State;
$strZip     = $details->Entry->Order->BillingAddress->PostalCode;
$strCountry     = $details->Entry->Order->BillingAddress->Country;

$strBillingFirstLastName = $details->Entry->Order->BillingName->FirstAndLast;

$strGrossValue    = $details->Entry->Order->OrderAmount;
$strNetValue      = $details->Entry->Order->SubTotal;
$strProcessingFee = $details->Entry->Order->ProcessingFees;
$strPaymentDate   = $details->Entry->Order->PaymentDate;

$strCardType  = $details->Entry->PaymentToken->Card->Brand;
$strCardLast4 = $details->Entry->PaymentToken->Card->Last4;

$strWhoToldYouAboutEzCard = $details->WhoToldYouAboutEZcard;

// Products
$strCardBuild = $details->CardBuild;
$strCardBuildValue = $details->CardBuild_Amount;
$strCardBuildPackage = $details->CardBuild_Value;

$strHostingNoPremium = $details->HostingNoPremium;
$strHostingNoPremiumValue = $details->HostingNoPremium_Amount;
$strHostingNoPremiumPackage = $details->HostingNoPremium_Value;

$arKeyword = $details->Keyword; // Array?
$strKeywordValue = $details->HostingNoPremium_Amount;
$strKeywordPackage = $details->HostingNoPremium_Value;

$strSetUpOptionsFree = $details->SetUpOptionsFree;
$strSetUpOptionsFreeValue = $details->SetUpOptionsFree_Amount;
$strSetUpOptionsFreePackage = $details->SetUpOptionsFree_Value;

$arSetUpOptions = $details->SetUpOptions; // Array?
$strSetUpOptionsValue = $details->SetUpOptions_Amount;
$strSetUpOptionsPackage = $details->SetUpOptions_Value;

if (!empty($details->AffiliateOptions))
{
    $strHosting = $details->AffiliateOptions;
    $strHostingValue = $details->AffiliateOptions_Amount;
    $strHostingPackage = $details->AffiliateOptions_Value;
}
else
{
    $strHosting = $details->Hosting;
    $strHostingValue = $details->Hosting_Amount;
    $strHostingPackage = $details->Hosting_Value;
}

$strHosting = $details->Hosting;
$strHostingValue = $details->Hosting_Amount;
$strHostingPackage = $details->Hosting_Value;

$strNFC = $details->NFC;
$strNFCValue = $details->NFC_Amount;

$objNewUser = new UserModel();

$objNewUser->user_id = $intNewUserId;
$objNewUser->company_id = $currCardData["company_id"];
$objNewUser->division_id = "0";
$objNewUser->username = $currCardData["username"];
$objNewUser->password = $currCardData["password"];
$objNewUser->created_on = (strtotime($currCardData["dateTimeAdded"]) === strtotime('0000-00-00 00:00:00') ? date("Y-m-d H:i:s") : $currCardData["dateTimeAdded"]);
$objNewUser->created_by = 1001;
$objNewUser->last_updated = date("Y-m-d H:i:s");
$objNewUser->updated_by = 1000;
$objNewUser->status = $currCardData["status"];
$objNewUser->first_name = $currCardData["firstName"];
$objNewUser->last_name = $currCardData["lastName"];
$objNewUser->display_name = $currCardData["displayName"];
$objNewUser->last_login = $currCardData["lastLogin"];

$objCustomerResult = (new Users())->createNew((object) $currUser, ["Customer"]);
