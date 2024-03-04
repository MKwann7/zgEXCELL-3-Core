<?php
/**
 * SHELL _site_core Extention for zgWeb.Solutions Web.CMS.App
 */

// Custom Escape String
use App\Core\AppModel;
use App\Utilities\Database;
use App\Utilities\Excell\ExcellCollection;
use App\Utilities\Transaction\ExcellTransaction;
use App\Utilities\Transaction\ExcellTransactionResult;

if (! function_exists('getFullUrl'))
{
    function getFullUrl()
    {
        global $app;
        return $app->objCoreData["Website"]['FullUrl'];
    }
}

if (! function_exists('getPublicUrl'))
{
    function getPublicUrl()
    {
        global $app;
        return $app->objAppSession["Core"]["App"]["Domain"]["Web"];
    }
}

if (! function_exists('getPortalUrl'))
{
    function getPortalUrl()
    {
        global $app;
        return $app->objAppSession["Core"]["App"]["Domain"]["Portal"];
    }
}

if (! function_exists('getFullPublicUrl'))
{
    function getFullPublicUrl()
    {
        global $app;
        return "http". ($app->objAppSession["Core"]["App"]["Domain"]["Web_SSL"] ? "s" : "") . "://" . $app->objAppSession["Core"]["App"]["Domain"]["Web"];
    }
}

if (! function_exists('getFullPortalUrl'))
{
    function getFullPortalUrl()
    {
        global $app;
        return "http". ($app->objAppSession["Core"]["App"]["Domain"]["Portal_SSL"] ? "s" : "") . "://" . $app->objAppSession["Core"]["App"]["Domain"]["Portal"];
    }
}

if (! function_exists('getFullActiveUrl'))
{
    function getFullActiveUrl()
    {
        global $app;
        return $app->getActiveDomain()->getDomainFull();
    }
}

if (! function_exists('getFullServerUrl'))
{
    function getFullServerUrl()
    {
        return env("SERVER_ENDPOINT");
    }
}

if (! function_exists('getIncomingIpAddress'))
{
    function getIncomingIpAddress() : string
    {
        $arIpAddresses = explode(",", $_SERVER["HTTP_X_FORWARDED_FOR"] ?? "");

        if (empty($arIpAddresses[0]))
        {
            return $_SERVER["REMOTE_ADDR"] ?? "";
        }

        return $arIpAddresses[0];
    }
}

// Custom Escape String
if (! function_exists('escapeString'))
{
    function escapeString($str)
    {
        $search = array("\\","\0","\n","\r","\x1a","'",'"');
        $replace = array("\\\\","\\0","\\n","\\r","\Z","\'",'\"');
        return str_replace($search,$replace,$str);
    }
}

if (! function_exists('dump'))
{
    function dump()
    {
        $objAllArgs = func_get_args();

        foreach($objAllArgs as $currArg)
        {
            if (empty($_SERVER['argv']) )
            {
                echo '<div class="zgPrint_div" style="background:#dddddd;padding:5px 15px;border:#cccccc;border-radius: 10px;margin-bottom:10px;">';
            }

            if (empty($_SERVER['argv']) )
            {
                echo '<pre style="white-space: pre-wrap;font-size:10px;line-height:12px;font-weight:bold;">';
            }

            if ( is_bool($currArg) )
            {
                echo (($currArg === true ) ? 'true' : 'false');
            }
            elseif (isInteger($currArg))
            {
                echo strval($currArg);
            }
            elseif (isDecimal($currArg))
            {
                echo strval($currArg);
            }
            elseif (!is_object($currArg) && is_string($currArg))
            {
                echo strval($currArg);
            }
            elseif ( is_array($currArg) || is_object($currArg) )
            {
                $arVarFilter = process_dump_value($currArg);
                print_r($arVarFilter);
            }
            else
            {
                echo 'Blank Variable/Array';
            }

            if (empty($_SERVER['argv']) )
            {
                echo '</pre>';
            }
            else
            {
                echo PHP_EOL;
            }

            if (empty($_SERVER['argv'])   )
            {
                echo '</div>';
            }
        }
    }
}

if (! function_exists('process_dump_value'))
{
    function process_dump_value($var)
    {
        $strClassType = gettype(type_cast_string($var));

        $objClass = [];
        $strClassName = "";

        switch($strClassType)
        {
            case "object":
                $strClassName = get_class($var);
                break;
            case "array":
                $strClassName = "Array";
                break;
            default:
                return $var;
                break;
        }


        foreach($var as $key => $value)
        {
            switch($strClassType)
            {
                case "object":
                case "array":
                    $objClass[$strClassName][$key] = process_dump_value($value);
                    break;
                default:
                    $objClass[$key] = type_cast_string($value);
                    break;
            }
        }

        return $objClass;
    }
}


if (! function_exists('type_cast_string'))
{
    function type_cast_string($strInput)
    {
        if (isInteger($strInput))
        {
            return (int) $strInput;
        }
        elseif (isDecimal($strInput))
        {
            return (double) $strInput;
        }
        elseif (isDateTime($strInput))
        {
            $strDate = date('Y-m-d H:i:s', strtotime($strInput));
            $strFormatedDate = DateTime::createFromFormat('Y-m-d H:i:s', $strDate);

            return $strFormatedDate->format('Y-m-d H:i:s');
        }
        elseif (is_array($strInput))
        {
            return (array) $strInput;
        }
        elseif (is_string($strInput))
        {
            return (string) $strInput;
        }
        else
        {
            return  $strInput;
        }
    }
}


if (! function_exists('isClass'))
{
    function isClass($strInput)
    {
        if (isInteger($strInput))
        {
            return false;
        }
        elseif (isDecimal($strInput))
        {
            return false;
        }
        elseif (isDateTime($strInput))
        {
            return false;
        }
        elseif (is_array($strInput))
        {
            return false;
        }
        elseif (is_string($strInput))
        {
            return false;
        }

        return true;
    }
}


if (! function_exists('dd'))
{
    function dd()
    {
        call_user_func_array('dump', func_get_args());
        die();
    }
}

if (! function_exists('emp'))
{
    function emp($e,$n,$x = null,$z = true)
    {
        if (empty($n) || $n == '' ) { $n == 'blank'; }
        if ( is_array($x) && $z == true && !empty($e) ) { $e = $x[0].$e.$x[1]; }
        return (!empty($e)?$e:$n);
    }
}

if (! function_exists('trace'))
{
    function trace()
    {
        $result = traceArray();

        return "\t" . implode("\n\t", $result);
    }
}

if (! function_exists('traceArray'))
{
    function traceArray()
    {
        $e = new Exception();
        $trace = explode("\n", $e->getTraceAsString());
        // reverse array to make steps line up chronologically
        $trace = array_reverse($trace);
        array_shift($trace); // remove {main}
        array_pop($trace); // remove call to this method
        $length = count($trace);
        $result = array();

        for ($i = 0; $i < $length; $i++)
        {
            $result[] = ($i + 1)  . ')' . substr($trace[$i], strpos($trace[$i], ' ')); // replace '#someNum' with '$i)', set the right ordering
        }

        return $result;
    }
}

if (! function_exists('getGuid'))
{
    function getGuid()
    {
        $data = random_bytes(16);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}

if (! function_exists('returnSelectedIfValuesMatch'))
{
    function returnSelectedIfValuesMatch($value_1, $value_2) : string
    {
        if ( $value_1 == $value_2)
        {
            return " selected";
        }

        return "";
    }
}

if (! function_exists('maxValueInArray'))
{
    function maxValueInArray($array, $keyToSearch)
    {
        $currentMax = NULL;
        foreach($array as $arr)
        {
            foreach($arr as $key => $value)
            {
                if ($key === $keyToSearch && ($value >= $currentMax))
                {
                    $currentMax = $value;
                }
            }
        }

        return $currentMax;
    }
}

if (! function_exists('buildSnakeCaseFromPascalCase'))
{
    function buildSnakeCaseFromPascalCase($strModelName) : string
    {
        $strModelFileName = preg_split('/(?=[A-Z])/',$strModelName ?? "");
        return str_replace(["_ ", " _"], "_",strtolower(implode("_",array_filter($strModelFileName))));
    }
}

if (! function_exists('buildHyphenLowercaseFromPascalCase'))
{
    function buildHyphenLowercaseFromPascalCase($strModelName) : string
    {
        $strModelFileName = preg_split('/(?=[A-Z])/',$strModelName);
        return strtolower(implode("-",array_filter($strModelFileName)));
    }
}

if (! function_exists('buildPascalCaseFromUnderscoreLowercase'))
{
    function buildPascalCaseFromUnderscoreLowercase($strControllerUri)
    {
        return str_replace(" ","",ucwords(str_replace("_"," ",str_replace("-","_", $strControllerUri))));
    }
}

if (! function_exists('buildControllerNameFromUri'))
{
    function buildControllerNameFromUri($strControllerUri)
    {
        return str_replace("-","_", $strControllerUri);
    }
}

if (! function_exists('buildControllerClassFromUri'))
{
    function buildControllerClassFromUri($strControllerUri)
    {
        return buildPascalCaseFromUnderscoreLowercase($strControllerUri);
    }
}

if (! function_exists('buildControllerMethodFromUri'))
{
    function buildControllerMethodFromUri($strControllerUri)
    {
        $strControllerMethod = buildPascalCaseFromUnderscoreLowercase($strControllerUri);
        return strtolower(substr($strControllerMethod,0,1)) . substr($strControllerMethod,1);
    }
}

if (! function_exists('buildModelFileFromName'))
{
    function buildModelFileFromName($strModelName)
    {
        return buildSnakeCaseFromPascalCase($strModelName);
    }
}

if (! function_exists('changeCollectionToArray'))
{
    function changeCollectionToArray($objEntityCollection)
    {
        $arEntityCollection = get_object_vars($objEntityCollection);
        return $arEntityCollection;
    }
}

if (! function_exists('userCan'))
{
    function userCan($strPermission, $objUser = null, $debug = false)
    {
        global $app;

        if (!is_a($objUser, \Entities\Users\Models\UserModel::class))
        {
            $objUser = $app->getActiveLoggedInUser($debug);
        }

        if ($objUser === null)
        {
            return false;
        }

        $userRoleClass = $objUser->Roles !== null ? ($objUser->Roles->FindEntityByKey("user_class_type_id")->user_class_type_id ?? null) : null;
        if ($userRoleClass === "Supreme") { return true; }

        switch($strPermission)
        {
            case "manage-ezpro-system":
            case "view-ezpro-contacts":
                if (empty($objUser->Roles))
                {
                    return false;
                }
                break;
            case "view-my-queue":
                if (empty($objUser->departmentTicketQueuesCount) || $objUser->departmentTicketQueuesCount === 0)
                {
                    return false;
                }
                break;
            case "view-system":
            case "manage-system":
            case "view-admin-customers":
            case "view-admin-users":
            case "view-admin-cards":
            case "view-admin-apps":
            case "view-admin-tickets":
            case "view-admin-notes":
            case "view-admin-packages":
            case "view-admin-reports":
                if (
                        !userIsCustomPlatform($userRoleClass)
                    )
                {
                    return false;
                }
                break;
            case "manage-platforms":
            case "view-super-customers":
            case "view-super-users":
            case "view-super-platforms":
            case "view-super-reports":

            if (
                !userIsEzDigital($userRoleClass)
            )
            {
                    return false;
                }

                break;

            case "manage-my-ezcards":
            case "manage-my-widget-library":
            case "view-my-card":
            case "view-my-apps":
            case "view-my-communication":
            case "view-my-contacts":
            case "share-my-card":
            case "get-new-card":
            case "view-image-library":
            case "view-video-library":
            case "view-social-library":
            default:
                return true;
                break;
        }

        return true;
    }
}

if (! function_exists('userIsCustomPlatform'))
{
    function userIsCustomPlatform($role)
    {
        if (
            !userIsEzDigital($role) &&
            $role !== "Custom Platform Admin" &&
            $role !== "Custom Platform Team Member" &&
            $role !== "Custom Platform Read-Only"
        )
        {
            return false;
        }

        return true;
    }
}

if (! function_exists('userIsEzDigital'))
{
    function userIsEzDigital($role)
    {
        if (
            $role !== "Supreme" &&
            $role !== "EZ Digital Admin" &&
            $role !== "EZ Digital Team Member" &&
            $role !== "EZ Digital Read-Only"
        )
        {
            return false;
        }

        return true;
    }
}

if (! function_exists('applicationGroupEnabledElement'))
{
    function applicationGroupEnabledElement($appGroupName)
    {
        $result = applicationGroupEnabled($appGroupName);

        if ($result !== true) {
            echo ' style="display:none;"';
        }

        echo "";
    }
}


if (! function_exists('applicationGroupEnabled'))
{
    function applicationGroupEnabled($appGroupName)
    {
        global $app;
        $groupsEnabled = explode(",",$app->objCustomPlatform->getCompanySettings()->FindEntityByValue("label","application_groups_enabled")->value ?? "");

        if (!in_array($appGroupName, $groupsEnabled)) {
            return false;
        }

        return true;
    }
}

if (! function_exists('userCanHideElement'))
{
    function userCanHideElement($strPermission)
    {
        $result = userCan($strPermission);

        if ($result !== true)
        {
            echo ' style="display:none;"';
        }

        echo  "";
    }
}

if (! function_exists('encryptPassword'))
{
    function encryptPassword($password)
    {
        $hash_format = "$2y$10$";   // Tells PHP to use Blowfish with a "cost" of 10

        $salt_length = 22;                  // Blowfish salts should be 22-characters or more

        $unique_random_string = md5(uniqid(mt_rand(), true));

        // Valid characters for a salt are [a-zA-Z0-9./]
        $base64_string = base64_encode($unique_random_string);

        // But not '+' which is valid in base64 encoding
        $modified_base64_string = str_replace('+', '.', $base64_string);

        // Truncate string to the correct length
        $salt = substr($modified_base64_string, 0, $salt_length);

        $format_and_salt = $hash_format . $salt;

        return crypt($password, $format_and_salt);
    }
}

if (! function_exists('passwordCheck'))
{
    function passwordCheck($password, $existing_hash) : bool
    {
        // existing hash contains format and salt at start
        $hash = crypt($password, $existing_hash);

        if($hash === $existing_hash)
        {
            return true;
        }

        return false;
    }
}

if (! function_exists('isIterable'))
{
    function isIterable($obj)
    {
        if (is_string($obj) || isBoolean($obj) || isInteger($obj) || isDecimal($obj) || isDateTime($obj) )
        {
            return false;
        }

        if (is_array($obj))
        {
            return true;
        }

        if (!is_array($obj))
        {
            $obj = (array) $obj;
        }

        if (count($obj) === 0)
        {

            return true;
        }

        return true;
    }
}

if (! function_exists('buildRecursiveArrayFromQueryString'))
{
    function buildRecursiveArrayFromQueryString($objRequestUriParameters)
    {
        $arParams = [];
        foreach ($objRequestUriParameters as $strRequestUriParameterFull )
        {
            $objRequestUriParameter = explode("=",$strRequestUriParameterFull);

            if (substr($objRequestUriParameter[0],-1) == ']')
            {
                $strBaseField = substr($objRequestUriParameter[0],0,strpos($objRequestUriParameter[0],"["));
                $arRequestUriKey = [];
                preg_match_all("/\[.*?\]/", $objRequestUriParameter[0], $arRequestUriKey, PREG_PATTERN_ORDER);

                if (empty($arParams[$strBaseField]))
                {
                    $arParams[$strBaseField] = recursiveAppendArrayListAsChildren([], $arRequestUriKey[0], trim(urldecode($objRequestUriParameter[1])));
                }
                else
                {
                    $arParams[$strBaseField] = recursiveAppendArrayListAsChildren($arParams[$strBaseField], $arRequestUriKey[0], trim(urldecode($objRequestUriParameter[1])));
                }
            }
            else
            {
                if (isset($objRequestUriParameter[1]))
                {
                    $arParams[$objRequestUriParameter[0]] = processUrlValues($objRequestUriParameter[1]);
                }
                else
                {
                    $arParams[] = processUrlValues($objRequestUriParameter[0]);
                }
            }
        }

        return $arParams;
    }
}

if (! function_exists('buildArrayFromQueryString'))
{
    function buildArrayFromQueryString($objRequestUriParameters)
    {
        $arParams = [];

        foreach ( $objRequestUriParameters as $strRequestUriParameterFull )
        {
            $objRequestUriParameter = explode("=",$strRequestUriParameterFull);
            if ( !empty($objRequestUriParameter[0]))
            {
                if(!empty($objRequestUriParameter[1]))
                {
                    $arParams[$objRequestUriParameter[0]] = processUrlValues($objRequestUriParameter[1]);
                }
                else
                {
                    $arParams[] = processUrlValues($objRequestUriParameter[0]);
                }
            }
        }

        return $arParams;
    }
}

if (! function_exists('recursiveAppendArrayListAsChildren'))
{
    function recursiveAppendArrayListAsChildren($data, $arr, $value)
    {
        $arRecursiveArray = $data;
        foreach($arr as $key => $field) {
            if (count($arr) > 1 && !empty($arRecursiveArray[substr($field,1,-1)])) {
                unset($arr[$key]);
                $arRecursiveArray[substr($field,1,-1)] = recursiveAppendArrayListAsChildren($arRecursiveArray[substr($field,1,-1)], $arr, $value);
                return $arRecursiveArray;
            } else {
                if (is_string($arRecursiveArray)) {
                    $arRecursiveArray = [];
                }
                $arRecursiveArray[substr($field,1,-1)] = $value;
                return $arRecursiveArray;
            }
        }
    }
}

if (! function_exists('darkenColorChannel'))
{
    function darkenColorChannel($colorValue, $darkeningValue)
    {
        $intNewColor = $colorValue - $darkeningValue;

        if ($intNewColor < 0) {
            $intNewColor = 0;
        }

        return $intNewColor;
    }
}

if (! function_exists('emp'))
{
    function emp($e, $n, $x = null, $z = true)
    {
        if (empty($n) || $n == '' ) { $n == 'blank'; }
        if (is_array($x) && $z == true && !empty($e)) { $e = $x[0].$e.$x[1]; }
        return (!empty($e)?$e:$n);
    }
}

if (! function_exists('isInteger'))
{
    function isInteger($strInput)
    {
        if (
            is_subclass_of($strInput, \App\Core\AppModel::class) ||
            is_a($strInput, 'stdClass') ||
            is_array($strInput)
        ) {
            return false;
        }

        if (!is_numeric($strInput)) { return false; }

        $strInput = str_replace("-", "", (string) $strInput);

        return ctype_digit(strval((string)$strInput));
    }
}

if (! function_exists('isDateTime'))
{
    function isDateTime($strInput)
    {
        if (
            !is_string($strInput)
        ) {
            return false;
        }

        $strInput = trim($strInput);

        if (strlen($strInput) < 11) { return false; }

        if (strpos(strtolower($strInput), "t") !== false) {
            $strInput = str_replace("t"," ", strtolower($strInput));
        }

        if (strpos($strInput, "+") !== false) {
            $arDateInput = explode("+", $strInput);
            $strInput = $arDateInput[0];
        }

        try {
            $strDate = date('Y-m-d H:i:s', strtotime($strInput));
            $strFormatedDate = DateTime::createFromFormat('Y-m-d H:i:s', $strDate);

            return strtotime($strInput) && $strFormatedDate && $strFormatedDate->format('Y-m-d H:i:s') === $strDate;
        } catch(\Exception $ex) {
            return false;
        }
    }
}

if (! function_exists('isDecimal'))
{
    function isDecimal($strInput)
    {
        if (
            is_subclass_of($strInput, \App\Core\AppModel::class) ||
            is_object($strInput) ||
            is_a($strInput, 'stdClass') ||
            is_array($strInput)
        ) {
            return false;
        }

        if(preg_match("/[a-z]/i", $strInput ?? "")) {
            return false;
        }

        if (isInteger($strInput)) {
            return true;
        }

        $strPattern = '/^[+-]?(\d*\.\d+([eE]?[+-]?\d+)?|\d+[eE][+-]?\d+)$/';

        return (!is_bool($strInput) && (is_float($strInput) || preg_match($strPattern, trim((string)$strInput))));
    }
}

if (! function_exists('isBoolean'))
{
    function isBoolean($strInput)
    {
        if (
            is_subclass_of($strInput, \App\Core\AppModel::class) ||
            is_a($strInput, 'stdClass') ||
            is_array($strInput)
        ) {
            return false;
        }

        return is_bool($strInput);
    }
}

if (! function_exists('isGuid'))
{
    function isGuid( $uuid )
    {
        if (!is_string($uuid) || (preg_match('/^[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}$/i', $uuid) !== 1)) {
            return false;
        }

        return true;
    }
}


if (! function_exists('isUuid'))
{
    function isUuid( $uuid )
    {
        if (!is_string($uuid) || (preg_match('/[a-f0-9]{8}\-[a-f0-9]{4}\-(8|9|a|b)[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}/', strtolower($uuid)) !== 1)) {
            return false;
        }

        return true;
    }
}


if (! function_exists('isStrongPassword'))
{
    function isStrongPassword( $uuid )
    {
        if (!is_string($uuid) || (preg_match('/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i', $uuid) !== 1)) {
            return false;
        }

        return true;
    }
}

if (! function_exists('isJson'))
{
    function isJson($objValue, $strName = "")
    {
        // decode the JSON data
        try
        {
            $strJsonString = "";

            if ( (is_array($objValue) || is_object($objValue) ) && !empty($objValue))
            {
                $objDataValueTest = json_decode(json_encode($objValue, JSON_FORCE_OBJECT), true);

                if ( count($objDataValueTest) > 0 )
                {
                    $objValueTransaction = new ExcellTransaction();
                    $objValueTransaction->data = $objDataValueTest;

                    $strJsonString = json_encode(Database::base64Encode($objValueTransaction)->data);
                }
                else
                {
                    $strJsonString = $objValue;
                }
            }
            else
            {
                $strJsonString = $objValue;
            }


            if (!is_string($strJsonString)) {
                return 'Object submitted for JSON parsing was not a string.';
            }

            json_decode($strJsonString);
            $error  = "";

            switch ( json_last_error() )
            {
                case JSON_ERROR_NONE:
                    break;
                case JSON_ERROR_DEPTH:
                    $error = 'The maximum stack depth has been exceeded.';
                    break;
                case JSON_ERROR_STATE_MISMATCH:
                    $error = 'Invalid or malformed JSON.';
                    break;
                case JSON_ERROR_CTRL_CHAR:
                    $error = 'Control character error, possibly incorrectly encoded.';
                    break;
                case JSON_ERROR_SYNTAX:
                    $error = 'Syntax error, malformed JSON.';
                    break;
                // PHP >= 5.3.3
                case JSON_ERROR_UTF8:
                    $error = 'Malformed UTF-8 characters, possibly incorrectly encoded.';
                    break;
                // PHP >= 5.5.0
                case JSON_ERROR_RECURSION:
                    $error = 'One or more recursive references in the value to be encoded.';
                    break;
                // PHP >= 5.5.0
                case JSON_ERROR_INF_OR_NAN:
                    $error = 'One or more NAN or INF values in the value to be encoded.';
                    break;
                case JSON_ERROR_UNSUPPORTED_TYPE:
                    $error = 'A value of a type that cannot be encoded was given.';
                    break;
                default:
                    $error = 'Unknown JSON error occured.';
                    break;
            }

            if ( $error !== "" )
            {
                return $error;
            }

            return true;
        }
        catch(Exception $ex)
        {
            return false;
        }
    }
}
if (! function_exists('ucwordsToLinux'))
{
    function ucwordsToLinux($param)
    {
        return strtolower(preg_replace('/(?<!^)([A-Z])/', '_\\1', $param));
    }
}

if (! function_exists('ucwordsToSentences'))
{
    function ucwordsToSentences($param)
    {
        return preg_replace('/(?<!^)([A-Z])/', ' \\1', $param);
    }
}

if (! function_exists('onlyAlphanumeric'))
{
    function onlyAlphanumeric($text)
    {
        return preg_replace("/[^a-zA-Z0-9]+/", "", $text);
    }
}

if (! function_exists('formatAsPhoneIfApplicable'))
{
    function formatAsPhoneIfApplicable($text)
    {
        if( isInteger($text) && strlen($text) === 10)
        {
            if(  preg_match( '/^(\d{3})(\d{3})(\d{4})$/', $text,  $matches ) )
            {
                return "(" . $matches[1] . ") " .$matches[2] . "-" . $matches[3];
            }
        }

        return $text;
    }
}

if (function_exists('mb_ereg_replace') && !function_exists("mb_escape"))
{
    function mb_escape(string $string)
    {
        return mb_ereg_replace('[\x00\x0A\x0D\x1A\x22\x25\x27\x5C\x5F]', '\\\0', $string);
    }
}
else
{
    if (!function_exists("mb_escape"))
    {
        function mb_escape(string $string)
        {
            return preg_replace('~[\x00\x0A\x0D\x1A\x22\x25\x27\x5C\x5F]~u', '\\\$0', $string);
        }
    }
}

if (! function_exists('logText'))
{
    // This will be moved to engine/libraries soon.
    function logText($strFileName, $strText)
    {
        $root = APP_CORE;

        if (!is_dir($root . '../logs/')) {
            mkdir($root . 'logs/');
            if (!is_dir($root . 'logs/')) {
                return array(
                    "0" => "zgError",
                    "1" => "Unable to make directory: " . (string)$root . 'logs/'
                );
            }
        }

        file_put_contents($root . "../logs/" . $strFileName, date("Y-m-d H:i:s") . ": " .$strText . PHP_EOL, FILE_APPEND);

        return array(
            "0" => "zgSuccess",
            "1" => "Logged: " . (string)$strText . ' at ' . $root . 'logs/' . $strFileName
        );
    }
}

if (! function_exists('excellErrorHandler'))
{
    function excellErrorHandler($errno, $errstr, $errfile, $errline)
    {
        if (!(error_reporting() & $errno)) {
            // This error code is not included in error_reporting, so let it fall
            // through to the standard PHP error handler
            return false;
        }

        switch ($errno) {
            case E_USER_ERROR:
                $strErrorText = "ERROR [$errno] $errstr: ".
                    "  Fatal error on line $errline in file $errfile".
                    ", PHP " . PHP_VERSION . " (" . PHP_OS . ")".
                    "Aborting...\n";
                break;

            case E_USER_WARNING:
                $strErrorText = "WARNING [$errno] $errstr\nFatal error on line $errline in file $errfile\n";
                break;

            case E_USER_NOTICE:
                $strErrorText = "NOTICE [$errno] $errstr\nFatal error on line $errline in file $errfile\n";
                break;

            default:
                $strErrorText = "Unknown error type: [$errno] $errstr\nFatal error on line $errline in file $errfile\n";
                break;
        }

        $trace = array_reverse(debug_backtrace());
        array_pop($trace);
        $trace = array_reverse($trace);

        foreach($trace as $item)
        {
            $strErrorText .= '  ' . (isset($item['file']) ? $item['file'] : '<unknown file>') . ' ' . (isset($item['line']) ? $item['line'] : '<unknown line>') . ' calling ' . $item['function'] . '()' . "\n";
        }

        if (env("APP_ENV") !== "production")
        {
            echo $strErrorText;
            logtext(date("Y-m-d") . ".ApplicationJs.Error.log", $strErrorText);
        }

        /* Don't execute PHP internal error handler */
        return true;
    }
}

set_error_handler("excellErrorHandler");

if (! function_exists('makeRecursiveDirectories'))
{
    function makeRecursiveDirectories($strRootPath, $strTargetDirectory)
    {
        $arTargetDirectories = array_filter(explode("/", $strTargetDirectory));
        $strFullPath = $strRootPath;

        foreach($arTargetDirectories as $currDirectoryFolder)
        {
            $strFullPath .= $currDirectoryFolder . "/";
            if(!is_dir($strFullPath))
            {
                if(!mkdir($strFullPath))
                {
                    logText("makeRecursiveDirectories.Error.log", "Error Making Directory: " . $strFullPath);
                }
            }
        }
    }
}

if (! function_exists('findFirstInteger'))
{
    function findFirstInteger()
    {
        $objAllArgs = func_get_args();

        if(empty($objAllArgs) || !is_array($objAllArgs))
        {
            return 0;
        }

        foreach( $objAllArgs as $strArgKey => $strArgObject )
        {
            if (!empty($strArgObject) && strtolower($strArgObject) !== "nan" && isInteger($strArgObject)) {
                return $strArgObject;
            }
        }

        return 0;
    }
}

if (! function_exists('processUrlValues'))
{
    function processUrlValues($objRawValue, $arOptions = [])
    {
        if (in_array(gettype($objRawValue), ["object","array","resource","unknown type"]))
        {
            return $objRawValue;
        }

        $objValue = trim(urldecode($objRawValue));

        return castValueTypes($objValue);
    }
}

if (! function_exists('castValueTypes'))
{
    function castValueTypes($objRawValue, $arOptions = [], $debug = false)
    {
        if (in_array(gettype($objRawValue), ["object","array","resource","unknown type"]))
        {
            return $objRawValue;
        }

        try
        {
            if (is_bool($objRawValue) === true)
            {
                if ($debug === true) { die("true boolean"); }
                return boolval($objRawValue);
            }

            $objValue = is_string($objRawValue) ? trim($objRawValue) : $objRawValue;

            if (strtolower($objValue ?? "") == "true")
            {
                if ($debug === true) { die("true string"); }
                return true;
            }
            elseif (strtolower($objValue ?? "") == "false")
            {
                if ($debug === true) { die("false string"); }
                return false;
            }
            elseif (isInteger($objValue))
            {
                if ($debug === true) { die("integer"); }
                return (int) $objValue;
            }
            elseif (isDecimal($objValue))
            {
                if ($debug === true) { die("decimal"); }
                return floatval($objValue);
            }
            elseif (isDateTime($objValue) && !in_array("no-date", $arOptions))
            {
                if ($debug === true) { die("datetime"); }
                return new DateTime($objValue);
            }
            else
            {
                return $objValue;
            }
        }
        catch(\Exception $ex)
        {
               return $objValue;
        }
    }
}

if (! function_exists('arrayToObject'))
{
    function arrayToObject($array)
    {
        return json_decode(json_encode($array));
    }
}

if (! function_exists('env'))
{
    function env($variableName, $defaultValue = "")
    {
        global $app;

        if ($app === null) {
            return $defaultValue;
        }

        $objData = $app->getEnv($variableName);

        if (empty($objData))
        {
            return $defaultValue;
        }

        return $objData;
    }
}

if (! function_exists('json_encode_advanced'))
{
    function json_encode_advanced(array $arr, $sequential_keys = false, $quotes = false, $beautiful_json = false) {

        $output = "{";
        $count = 0;
        foreach ($arr as $key => $value) {

            if ( isAssoc($arr) || (!isAssoc($arr) && $sequential_keys == true ) ) {
                $output .= ($quotes ? '"' : '') . $key . ($quotes ? '"' : '') . ' : ';
            }

            if (is_array($value)) {
                $output .= json_encode_advanced($value, $sequential_keys, $quotes, $beautiful_json);
            } else if (is_bool($value)) {
                $output .= ($value ? 'true' : 'false');
            } else if (is_numeric($value)) {
                $output .= $value;
            } else if ($value === '') {
                $output .= "''";
            } else {
                $output .= ($quotes || $beautiful_json ? '"' : '') . $value . ($quotes || $beautiful_json ? '"' : '');
            }

            if (++$count < count($arr)) {
                $output .= ', ';
            }
        }

        $output .= "}";

        return $output;
    }
}

if (! function_exists('isAssoc'))
{
    function isAssoc(array $arr) {
        if (array() === $arr) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}

if (! function_exists('getClassData'))
{
    function getClassData($classPath): array
    {
        $content = explode("\n", file_get_contents($classPath));

        $objClassInstanceName = "";

        foreach($content as $currLine)
        {
            if (substr($currLine, 0, 9) === "namespace") {
                $objClassInstanceName .= trim(str_replace(["namespace ", ";"], "", $currLine));
                continue;
            }

            if (substr($currLine, 0, 5) === "class")
            {
                $classNameFull = trim(str_replace("class ", "", $currLine));

                if (strpos($classNameFull, " extends ") !== false)
                {
                    $classNameFull = explode(" extends ", $classNameFull)[0];
                }

                $objClassInstanceName .= "\\".$classNameFull;
                break;
            }
        }

        $objClassInstanceNameArray        = explode("\\", $objClassInstanceName);
        $currClassIndex                   = array_pop($objClassInstanceNameArray);

        return [$currClassIndex, $objClassInstanceName];
    }
}