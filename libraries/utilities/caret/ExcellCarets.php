<?php

namespace App\Utilities\Caret;

class ExcellCarets
{
    public function processExternalCarets($componentObject, $props) : string
    {
        $componentObject = str_replace("[EZDIGITAL__AUTH]", ((!empty($props->authSession)) ? '"'.$props->authSession.'"' : "'inactive'"), $componentObject);
        $componentObject = str_replace("[EZDIGITAL__AUTH_ID]", ((!empty($props->authSessionId)) ? '"'.$props->authSessionId.'"' : "null"), $componentObject);

        return $componentObject;
    }
    public function processInternalCarets($componentObject) : string
    {
        $carets = new \stdClass();

        $carets->authSession = $_COOKIE["authId"] ? "active" : "inactive";
        $carets->authSessionId = $_COOKIE["authId"] ?? null;

        return $this->processExternalCarets($componentObject, $carets);
    }
}