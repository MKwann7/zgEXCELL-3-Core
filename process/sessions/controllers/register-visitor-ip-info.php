<?php
/**
 * ENGINECORE _site_core Extention for zgWeb.Solutions Web.CMS.App
 */

use Entities\Visitors\Classes\VisitorBrowser;
use Entities\Visitors\Classes\Visitors;
use Entities\Visitors\Models\VisitorModel;

$objSessionCheck = $app->objHttpRequest->Data->PostData;

$blnRegisteredActivity = false;

if ( $app->blnLoggedIn === false)
{
    if(empty($app->objAppSession["Core"]["Session"]["IpInfo"]) || !is_array($app->objAppSession["Core"]["Session"]["IpInfo"]))
    {
        $blnRegisteredActivity = true;

        unset($objSessionCheck->success);

        $app->objAppSession["Core"]["Session"]["IpInfo"] = $objSessionCheck;
        $app->objAppSession["Core"]["Session"]["IpInfo"]->guid = getGuid();

        $strBrowserCookie = $_COOKIE['instance'];
        $objBrowserCookie = (new VisitorBrowser())->getWhere(["browser_cookie" => $strBrowserCookie])->getData()->first();

        if (!empty($objBrowserCookie))
        {
            $objBrowserCookie->browser_type = $objSessionCheck->browser;
            $objBrowserCookie->browser_ip = $objSessionCheck->ip;
            (new VisitorBrowser())->update($objBrowserCookie);
        }

        $objVisitor = new VisitorModel();

        $objVisitor->user_id = (isset($objSessionCheck->user_id) ? $objSessionCheck->user_id : null);
        $objVisitor->card_id           = (isset($objSessionCheck->card_id) ? $objSessionCheck->card_id : null);
        $objVisitor->visitor_activity_guid = $app->objAppSession["Core"]["Session"]["IpInfo"]->guid;
        $objVisitor->visitor_browser_id = $objBrowserCookie->visitor_browser_id;
        $objVisitor->activity_type     = "website_visitor";
        $objVisitor->created_on        = date("Y-m-d H:i:s");
        $objVisitor->ip_address        = (isset($objSessionCheck->ip) ? $objSessionCheck->ip : null);
        $objVisitor->address_city = (isset($objSessionCheck->city) ? $objSessionCheck->city : null);
        $objVisitor->address_state = (isset($objSessionCheck->region) ? $objSessionCheck->region : null);
        $objVisitor->address_zip = (isset($objSessionCheck->postal) ? $objSessionCheck->postal : null);
        $objVisitor->address_country = (isset($objSessionCheck->country) ? $objSessionCheck->country : null);
        $objVisitor->address_loc = (isset($objSessionCheck->loc) ? $objSessionCheck->loc : null);
        $objVisitor->visitor_data = json_encode($objSessionCheck);

        $objTest = (new Visitors())->createNew($objVisitor);

        die(json_encode($objVisitor));
    }

    if ($blnRegisteredActivity === false && !empty($objSessionCheck->card_id) && $objSessionCheck->card_id != 0)
    {
        $blnReturningVisitor = false;
        $blnRegisteredActivity = true;

        if ( is_array($app->objAppSession["Core"]["Session"]["Card"]["CardHistory"]) )
        {
            foreach ( $app->objAppSession["Core"]["Session"]["Card"]["CardHistory"] as $strKey => $objData)
            {
                if ( $objSessionCheck->card_id === $strKey )
                {
                    $blnReturningVisitor = true;
                }
            }
        }

        if ( $blnReturningVisitor == false )
        {
            $app->objAppSession["Core"]["Session"]["Card"]["ActiveCard"] = $objSessionCheck->card_id;
            $app->objAppSession["Core"]["Session"]["Card"]["CardHistory"][$objSessionCheck->card_id] = date("Y-m-d H:i:s");

            unset($objSessionCheck->success);

            $app->objAppSession["Core"]["Session"]["IpInfo"]       = $objSessionCheck;
            $app->objAppSession["Core"]["Session"]["IpInfo"]->guid = getGuid();

            $strBrowserCookie = $_COOKIE['instance'];
            $objBrowserCookie = (new VisitorBrowser())->getWhere(["browser_cookie" => $strBrowserCookie])->getData()->first();

            if (!empty($objBrowserCookie))
            {
                $objBrowserCookie->browser_type = $objSessionCheck->browser;
                (new VisitorBrowser())->update($objBrowserCookie);
            }

            $objVisitor = new VisitorModel();

            $objVisitor->user_id       = (isset($objSessionCheck->user_id) ? $objSessionCheck->user_id : null);
            $objVisitor->card_id           = (isset($objSessionCheck->card_id) ? $objSessionCheck->card_id : null);
            $objVisitor->visitor_activity_guid = $app->objAppSession["Core"]["Session"]["IpInfo"]->guid;
            $objVisitor->visitor_browser_id = $objBrowserCookie->visitor_browser_id;
            $objVisitor->activity_type     = "website_visitor";
            $objVisitor->created_on        = date("Y-m-d H:i:s");
            $objVisitor->ip_address        = (isset($objSessionCheck->ip) ? $objSessionCheck->ip : null);;
            $objVisitor->address_city = (isset($objSessionCheck->city) ? $objSessionCheck->city : null);;
            $objVisitor->address_state = (isset($objSessionCheck->region) ? $objSessionCheck->region : null);;
            $objVisitor->address_zip = (isset($objSessionCheck->postal) ? $objSessionCheck->postal : null);;
            $objVisitor->address_country = (isset($objSessionCheck->country) ? $objSessionCheck->country : null);;
            $objVisitor->address_loc = (isset($objSessionCheck->loc) ? $objSessionCheck->loc : null);;
            $objVisitor->visitor_data = json_encode($objSessionCheck);

            $objTest = (new Visitors())->createNew($objVisitor);

            die(json_encode($objVisitor));
        }
    }
}
else
{
    if(empty($app->objAppSession["Core"]["Session"]["IpInfo"]) || !is_array($app->objAppSession["Core"]["Session"]["IpInfo"]))
    {
        unset($objSessionCheck->success);

        $app->objAppSession["Core"]["Session"]["IpInfo"]       = $objSessionCheck;
        $app->objAppSession["Core"]["Session"]["IpInfo"]->guid = getGuid();
    }
}

echo json_encode($app->objAppSession["Core"]["Session"]["IpInfo"]);
die;
