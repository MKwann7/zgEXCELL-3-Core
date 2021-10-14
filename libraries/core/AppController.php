<?php

namespace App\Core;

use App\Utilities\Excell\ExcellIterator;

class AppController extends ExcellIterator
{
    /**
     * @var App $app;
     */
    protected $app;

    /**
     * @var AppEntity $AppEntity;
     */
    protected $AppEntity;

    protected $validationErrors = [];

    public function __construct($app)
    {
        $this->app = $app;
    }

    protected function validateRequestType() : bool
    {
        return (in_array(strtolower($this->app->objHttpRequest->Verb), explode(",", strtolower(implode(",", func_get_args()))), true));
    }

    protected function getRequestType() : string
    {
        return strtolower($this->app->objHttpRequest->Verb);
    }

    protected function validate($data, $rules) : bool
    {
        $data = json_decode(json_encode($data), true);
        $this->validationErrors = [];

        foreach($rules as $fieldName => $ruleDefinitions)
        {
            if (strpos($ruleDefinitions, "required") !== false && (!isset($data[$fieldName]) || $data[$fieldName] === ''))
            {
                $this->validationErrors[$fieldName][] = "{$fieldName} is not present";
            }

            if (strpos($ruleDefinitions, "integer") !== false && !empty($data[$fieldName]) && !isInteger($data[$fieldName]))
            {
                $this->validationErrors[$fieldName][] = "{$fieldName} is not an integer";
            }

            if (strpos($ruleDefinitions, "decimal") !== false && !empty($data[$fieldName]) && !isDecimal($data[$fieldName]))
            {
                $this->validationErrors[$fieldName][] = "{$fieldName} is not an decimal";
            }

            if (strpos($ruleDefinitions, "datetime") !== false && !empty($data[$fieldName]) && !isDateTime($data[$fieldName]))
            {
                $this->validationErrors[$fieldName][] = "{$fieldName} is not an datetime";
            }

            if (strpos($ruleDefinitions, "uuid") !== false && !empty($data[$fieldName]) && !isGuid($data[$fieldName]))
            {
                $this->validationErrors[$fieldName][] = "{$fieldName} is not an guid";
            }

            if (strpos($ruleDefinitions, "email") !== false && !empty($data[$fieldName]) && !filter_var($data[$fieldName], FILTER_VALIDATE_EMAIL))
            {
                $this->validationErrors[$fieldName][] = "{$fieldName} is not an email address";
            }
        }

        return count($this->validationErrors) === 0;
    }

    protected function renderView(string $view = "") : void
    {
        die($view);
    }

    protected function renderReturnJson($blnSuccess = false, $objData = null, $strMessage = "", $code = 200, $strDataLabel = "data", $end = null) : void
    {
        $objTransaction = [
            "success" => $blnSuccess,
            "message" => $strMessage
        ];

        if ($end !== null)
        {
            $objTransaction["end"] = $end;
        }

        if(!empty($objData))
        {
            $objTransaction[$strDataLabel] = $objData;
        }

        header('Access-Control-Allow-Origin: '.$_SERVER['HTTP_ORIGIN']);
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, RequestType');
        header('Content-Type: application/json');

        echo json_encode($objTransaction);
        die;
    }

    protected function renderReturnCachedJson($blnSuccess = false, $objData = null, $strMessage = "", $code = 200, $strDataLabel = "data", $end = null) : void
    {
        $objTransaction = [
            "success" => $blnSuccess,
            "message" => $strMessage
        ];

        if ($end !== null)
        {
            $objTransaction["end"] = $end;
        }

        if(!empty($objData))
        {
            $objTransaction[$strDataLabel] = $objData;
        }

        header('Access-Control-Allow-Origin: '.$_SERVER['HTTP_ORIGIN']);
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, RequestType');
        header('Content-Type: application/json');
        header('Cache-Control: max-age=3600');
        header('ETag: "'.md5(date("Y-m-d")).'"');

        echo json_encode($objTransaction);
        die;
    }

    public function setAppModule(&$objModule) : void
    {
        $this->AppEntity = $objModule;
    }

    protected function sendSuccessfulResponseAndContinue($message = "completed", $data = null) : void
    {
        ignore_user_abort(true);
        set_time_limit(0);
        ob_start();
        $serverProtocole = filter_input(INPUT_SERVER, 'SERVER_PROTOCOL', FILTER_SANITIZE_STRING);
        header($serverProtocole.' 200 OK');
        echo '{"success":true, "message":"'.$message.'", "data":'. ($data === null ? "null" : json_encode($data)).'}';
        header('Connection: close');
        header('Content-Length: '.ob_get_length());
        ob_end_flush();
        ob_flush();
        flush();
        fastcgi_finish_request();
    }
}