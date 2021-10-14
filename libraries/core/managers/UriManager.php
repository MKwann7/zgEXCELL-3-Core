<?php

namespace App\Core\Managers;

use App\Utilities\Excell\ExcellHttpModel;

class UriManager
{
    private $httpRequest;

    public function __construct(ExcellHttpModel $httpRequest)
    {
        $this->httpRequest = $httpRequest;
    }

    public function build()
    {
        if (empty($this->httpRequest))
        {
            return $this;
        }

        // build current uri folder request
        $strFullUriRequstPath = '';

        foreach ( $this->httpRequest->Uri as $currUriFolderLabel )
        {
            $strFullUriRequstPath .= $currUriFolderLabel . '/';
        }

        $strFullUriRequstPath  = substr($strFullUriRequstPath, 0, -1);
        $this->httpRequest->PathUri = $strFullUriRequstPath;

        // Build current uri parameter requests
        $strFullRequestParamString = '?';

        unset ( $this->httpRequest->Params['uripagerequest'] );

        foreach ( $this->httpRequest->Params as $currRequestParamsLabel => $currRequestParamsValue )
        {
            $strFullRequestParamString .= $currRequestParamsLabel . '=' . $currRequestParamsValue . '&';
        }

        $strFullRequestParamString = substr($strFullRequestParamString, 0, -1);
        $this->httpRequest->RequestParamsOriginal = $strFullRequestParamString;

        if ( !empty($strFullRequestParamString) )
        {
            $this->httpRequest->PathFull = $strFullUriRequstPath . $strFullRequestParamString;
        }
        else
        {
            $this->httpRequest->PathFull = $strFullUriRequstPath;
        }

        return $this;
    }

    public function parse()
    {
        if (empty($this->httpRequest))
        {
            return $this;
        }

        $this->httpRequest->LoadServerData();
        $this->httpRequest->Params = [];

        $objRequestUriPath = explode("/", $this->httpRequest->UriOriginal);

        if (!empty($_SERVER["QUERY_STRING"]))
        {
            $objRequestUriParameters = explode("&",$_SERVER["QUERY_STRING"]);

            if ($this->httpRequest->UriOriginal != "")
            {
                $this->httpRequest->Params = buildRecursiveArrayFromQueryString($objRequestUriParameters);
            }
            else
            {
                $this->httpRequest->Params = buildArrayFromQueryString($objRequestUriParameters);
            }
        }

        if (count($objRequestUriPath) >= 1)
        {
            $this->httpRequest->Uri = array_values(array_filter($objRequestUriPath));
            return $this;
        }

        $this->httpRequest->Uri = array("/");

        return $this;
    }

    public function get()
    {
        return $this->httpRequest;
    }
}