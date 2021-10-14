<?php
/**
 * ENGINECORE _site_core Extention for zgWeb.Solutions Web.CMS.App
 */

use Entities\Users\Classes\Users;
use Entities\Visitors\Classes\VisitorBrowser;

if(!$app->isPost())
{
    die('{"success":false,"message":"You are not authorized to access this."}');
}

$intUserId = $app->objAppSession["Core"]["Account"]["Primary"];

foreach($app->objAppSession["Core"]["Account"]["Active"] as  $intSessionKey => $objActiveSessions)
{
    if ( $intUserId === $objActiveSessions["user_id"])
    {

        if(!empty($app->objAppSession["Core"]["Account"]["Active"][$intSessionKey]["impersonate"]))
        {
            $intReturningActiveUserId = $app->objAppSession["Core"]["Account"]["Active"][$intSessionKey]["impersonate"];
            $objReturningUserResult = (new Users())->getById($intReturningActiveUserId);

            if ($objReturningUserResult->Result->Count === 1)
            {

                unset($app->objAppSession["Core"]["Account"]["Active"][$intSessionKey], $app->objAppSession["Core"]["Account"]["Primary"], $app->objAppSession["Core"]["App"]["Domain"]["Portal"], $app->objAppSession["Core"]["App"]["Domain"]["Web"]);

                $strBrowserCookie = $_COOKIE["instance"];
                $objBrowserCookieResult = (new VisitorBrowser())->getWhere(["browser_cookie" => $strBrowserCookie]);

                if ($objBrowserCookieResult->Result->Success === true)
                {
                    $objBrowserCookie = $objBrowserCookieResult->Data->First();

                    if (!empty($objBrowserCookie))
                    {
                        $objBrowserCookie->user_id = $intReturningActiveUserId;
                        $objBrowserCookie->logged_in_at = date("Y-m-d H:i:s");
                        (new VisitorBrowser())->update($objBrowserCookie);
                    }
                }

                $app->objAppSession["Core"]["Account"]["Primary"] = $intReturningActiveUserId;

                die('{"success":true, "redirect": "/account", "message":"You\'ve successfully logged out!"}');
            }
        }
        else
        {
            if(!empty(($app->objAppSession["Core"]["Account"]["Active"]) && count($app->objAppSession["Core"]["Account"]["Active"]) > 0))
            {
                $intNewActiveUserId = "";

                unset($app->objAppSession["Core"]["Account"]["Active"][$intSessionKey], $app->objAppSession["Core"]["Account"]["Primary"], $app->objAppSession["Core"]["App"]["Domain"]["Portal"], $app->objAppSession["Core"]["App"]["Domain"]["Web"]);

                foreach($app->objAppSession["Core"]["Account"]["Active"] as $arActiveUsers)
                {
                    $intNewActiveUserId = $arActiveUsers["user_id"];
                }

                $objUserResult = (new Users())->getById($intNewActiveUserId);

                if ($objUserResult->Result->Count === 1)
                {
                    $app->objAppSession["Core"]["Account"]["Primary"] = $intNewActiveUserId;
                    $app->setActiveLoggedInUser($objUserResult->Data->First());

                    die('{"success":true, "redirect": "/account", "message":"You\'ve successfully logged out!"}');
                }

                $strBrowserCookie = $_COOKIE["instance"];
                $objBrowserCookieResult = (new VisitorBrowser())->getWhere(["browser_cookie" => $strBrowserCookie]);

                if ($objBrowserCookieResult->Result->Success === true)
                {
                    $objBrowserCookie = $objBrowserCookieResult->Data->First();

                    if (!empty($objBrowserCookie))
                    {
                        //$objBrowserCookie->user_id      = ($intNewActiveUserId == "" ? ExcellNull : $intNewActiveUserId);
                        $objBrowserCookie->logged_in_at = ExcellNull;
                        $result = (new VisitorBrowser())->update($objBrowserCookie);
                    }
                }
            }
        }

        unset($app->objAppSession["Core"]["Account"]["Active"][$intSessionKey]);
        unset($app->objAppSession["Core"]["Account"]["Primary"]);

        die('{"success":true,"message":"You\'ve successfully logged out!"}');
    }
}

die('{"success":false,"message":"Your login could not be revoked!"}');
