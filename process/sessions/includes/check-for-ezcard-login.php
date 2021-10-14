<?php
/**
 * ENGINECORE _site_core Extention for zgWeb.Solutions Web.CMS.App
 */

session_start();
require __DIR__ . '/../../../../util/purchase-functions.php';
require __DIR__ . '/../../../../_connections/db.php';
require __DIR__ . '/../../../../_includes/included_functions.php';
require __DIR__ . '/../../../../engine/libraries/custom.functions.php';
require __DIR__ . '/../../../../engine/libraries/core.class.php';

if(empty($_SESSION["session"]["authentication"]) || !is_array($_SESSION["session"]["authentication"]))
{
    $_SESSION["session"]["authentication"] = array(
        "username" => rand(10000,99999),
        "password" => rand(10000,99999)
    );
}

$objActiveLogins = array();

if(!empty($_SESSION["account"]["active"]) && is_array($_SESSION["account"]["active"]))
{
    foreach ( $_SESSION["account"]["active"] as $strIndex => $objData )
    {
        if ( strtotime($objData["start_time"]) > strtotime("-48 hours"))
        {
            $resultCustomers = null;

            if ( $objData["type"] == "admin" )
            {
                $queryCustomers  = "SELECT * ";
                $queryCustomers  .= "FROM users ";
                $queryCustomers  .= "WHERE ver = '{$objData["ver"]}'";
                $resultCustomers = mysqli_query($connection, $queryCustomers);
            }
            else
            {
                $queryCustomers  = "SELECT * ";
                $queryCustomers  .= "FROM customers ";
                $queryCustomers  .= "WHERE ver = '{$objData["ver"]}'";
                $resultCustomers = mysqli_query($connection, $queryCustomers);
            }
            while ( $currCustomer = mysqli_fetch_assoc($resultCustomers) )
            {
                $objActiveLogins[] = $currCustomer;
            }
        }
    }
}

$blnUserLoggedIn = false;

if ( count($objActiveLogins) > 0 )
{
    $blnUserLoggedIn = true;
}

// This will be moved to engine/libraries soon.
function logText($strFileName, $strText)
{
    $root = '/home/srv/www/public_html/';

    if ( !is_dir($root . 'logs/') )
    {
        mkdir($root . 'logs/');
        if ( !is_dir($root . 'logs/') )
        {
            return array(
                "0" => "zgError",
                "1" => "Unable to make directory: " . (string)$root . 'logs/'
            );
        }
    }

    file_put_contents($root . "logs/" . $strFileName, $strText . PHP_EOL, FILE_APPEND);

    return array(
        "0" => "zgSuccess",
        "1" => "Logged: " . (string)$strText . ' at ' . $root . 'logs/' . $strFileName
    );
}