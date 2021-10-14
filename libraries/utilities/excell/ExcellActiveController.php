<?php

namespace App\Utilities\Excell;

class ExcellActiveController
{
    public $Module;
    public $Controller;
    public $Method;
    public $Active;
    public $Type;
    public $UriMethodRequestRoot;

    public function __construct($Active, $Type = 'default', $Module = null, $Controller = null, $Method = null, $UriMethodRequestRoot = null)
    {
        $this->Active = $Active;
        $this->Type = $Type;
        $this->Module = $Module;
        $this->Controller = $Controller;
        $this->Method = $Method;
        $this->UriMethodRequestRoot = $UriMethodRequestRoot;
    }
}