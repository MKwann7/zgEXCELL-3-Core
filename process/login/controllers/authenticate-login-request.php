<?php
/**
 * ENGINECORE _site_core Extention for zgWeb.Solutions Web.CMS.App
 */

use Entities\Users\Classes\Users;
use Entities\Users\Models\UserModel;

if(!$app->isPost())
{
    die('{"success":false,"message":"You are not authorized to access this."}');
}

$objImage = new UserModel();
$objImage->username = $app->objHttpRequest->Data->PostData->username ?? "";
$objImage->password = $app->objHttpRequest->Data->PostData->password ?? "";

$objUsers = new Users();
$objUserAuthentication = $objUsers->AuthenticateUserForLogin($objImage);

if ( $objUserAuthentication->result->Success === false)
{
    die('{"success":false,"message":"'.$objUserAuthentication->result->Message.'"}');
}

$user = $objUserAuthentication->getData()->first();

$sessionId = $objUsers->setUserLoginSessionData($user, $_COOKIE['instance']);
$objUsers->setUserActiveCookies($sessionId);
$objUsers->setUserLoginCookies($user);

$app->setActiveLoggedInUser($user);

$instance = $_COOKIE['instance'];
$userId = $user->toArray(["sys_row_id"])["sys_row_id"];
$userNum = $user->toArray(["user_id"])["user_id"];

unset($app->objAppSession["Core"]["App"]["Domain"]["Portal"], $app->objAppSession["Core"]["App"]["Domain"]["Web"]);

if (!empty($app->objAppSession["Core"]["Session"]["RedirectAfterLogin"]))
{
    $strRedirectAfterLogin = $app->objAppSession["Core"]["Session"]["RedirectAfterLogin"];
    unset($app->objAppSession["Core"]["Session"]["RedirectAfterLogin"]);
    die('{"success":true,"url":"/' . $strRedirectAfterLogin . '","message":"'.$objUserAuthentication->result->Message.'","data": {"userId": "'.$userId.'","instance": "'.$instance.'"} }');
}

$userArray = $user->toArray(["first_name","last_name","user_email","user_phone"]);
$userArray["Roles"] = $user->Roles?->ToPublicArray();
$userArray["Departments"] = $user->Departments?->ToPublicArray();

die('{"success":true,"url":"/account","message":"'.$objUserAuthentication->result->Message.'","data": {"userId": "'.$userId.'","instance": "'.$instance.'","userNum": '.$userNum.', "user": '.json_encode($userArray).'} }');
