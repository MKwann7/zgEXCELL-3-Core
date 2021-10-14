<?php

namespace App\Website\Constructs;

class Breadcrumb
{
    protected $label;
    protected $link;

    public function __construct ($label, $link, $type = "link")
    {
        $this->label = $label;
        $this->link = $link;
        $this->type = $type;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function getLink()
    {
        return $this->link;
    }

    public function getType()
    {
        return $this->type;
    }
}