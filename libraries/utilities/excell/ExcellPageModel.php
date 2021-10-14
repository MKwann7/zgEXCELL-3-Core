<?php

namespace App\Utilities\Excell;

class ExcellPageModel
{
    public $H1Tag;
    public $BodyId;
    public $BodyClasses = [];
    public $Meta;
    public $SnipIt;
    public $Columns;
    public $Template;

    public function __construct()
    {
        $this->Meta = new ExcellPageMetaModel();
        $this->SnipIt = new ExcellPageSnipitModel();
        $this->Template = new ExcellPageTemplateModel();
    }
}