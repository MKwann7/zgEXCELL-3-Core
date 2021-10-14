<?php

namespace App\Utilities\Excell;

class ExcellHttpModel
{
    public $OriginalUriAndParams;
    public $Params = [];
    public $UriOriginal;
    public $Uri = [];
    public $PathControllerBase;
    public $PathFull;
    public $PathUri;
    public $RequestParamsOriginal;
    public $Verb;
    public $ParameterCoreFunctionRequests = []; // This is for future functionality
    public $HeaderData;
    public $UserName;
    public $Password;
    public $Data;
    public $HasModelData;
    public $ValidModelData;
    public $AuthenticatedModelName;
    public $AuthenticatedModelType;

    public function __construct()
    {
        $this->Data = new ExcellHttpResponseModel();
        $this->HeaderData = new ExcellCollection();
    }

    public function LoadServerData() : void
    {
        $this->UriOriginal = $this->generateUriOriginal();;
        $this->OriginalUriAndParams = $_SERVER["QUERY_STRING"] ?? "";
        $this->HeaderData->AcceptEncoding = $_SERVER["HTTP_ACCEPT_ENCODING"] ?? "";
        $this->HeaderData->AcceptLanguage = $_SERVER["HTTP_ACCEPT_LANGUAGE"] ?? "";
        $this->HeaderData->Cookie = $_SERVER["HTTP_COOKIE"] ?? "";
        $this->HeaderData->Port = $_SERVER["SERVER_PORT"] ?? "";
        $this->HeaderData->IncomingIpAddress = getIncomingIpAddress();
        $this->HeaderData->UserAgent = $_SERVER["HTTP_USER_AGENT"] ?? "";
        $this->HeaderData->RequestType = $_SERVER["HTTP_REQUESTTYPE"] ?? "DEFAULT";
        $this->processHeaderAuthorizations();
        $this->HeaderData->DateTime = date("Y-m-d\TH:i:sT");
    }

    private function processHeaderAuthorizations() : void
    {
        if (!empty($_SERVER["HTTP_AUTHORIZATION"]))
        {
            $authData = base64_decode(str_replace("Basic ", "", $_SERVER["HTTP_AUTHORIZATION"]));

            if (strpos($authData, ":") === false)
            {
                return;
            }

            $arAuthData = explode(":", $authData);

            $this->UserName = $arAuthData[0] ?? "";
            $this->Password = $arAuthData[1] ?? "";
        }
    }

    public function setBaseUri(int $methodIndex, $methodName = "index") : self
    {
        $this->PathControllerBase = implode("/", array_slice($this->Uri, 0, $methodIndex));

        if ($methodName !== "index")
        {
            $this->PathControllerBase .= "/" . $methodName;
        }

        return $this;
    }

    private function generateUriOriginal()
    {
        $strGenerateUriOriginal = escapeString(strip_tags(substr($_SERVER["REQUEST_URI"] ?? "", 1)));

        if (strpos($strGenerateUriOriginal, "?") !== false)
        {
            return explode("?", $strGenerateUriOriginal)[0];
        }

        return $strGenerateUriOriginal;
    }
}