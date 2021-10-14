<?php

namespace App\Utilities\Excell;

abstract class ExcellIterator implements \Iterator
{
    protected $Properties = [];

    public function rewind()
    {
        reset($this->Properties);
    }

    public function current()
    {
        return current($this->Properties);
    }

    public function key()
    {
        return key($this->Properties);
    }

    public function next()
    {
        return next($this->Properties);
    }

    public function valid()
    {
        $key = key($this->Properties);
        return ($key !== NULL && $key !== FALSE);
    }
}