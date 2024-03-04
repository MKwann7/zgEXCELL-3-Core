<?php

namespace App\Website\Constructs;

class Breadcrumb
{
    private string $label;
    private string $link;
    private $type;

    public function __construct ($label, $link, $type = "link")
    {
        $this->label = $label;
        $this->link = $link;
        $this->type = $type;
    }

    public function getLabel() : string
    {
        return $this->label;
    }

    public function getLink() : string
    {
        return $this->link;
    }

    public function getType() : string
    {
        return $this->type;
    }
}