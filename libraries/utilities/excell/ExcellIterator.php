<?php

namespace App\Utilities\Excell;

abstract class ExcellIterator implements \Iterator
{
    protected array $Properties = [];

    public function rewind(): void
    {
        reset($this->Properties);
    }

    public function current(): mixed
    {
        return current($this->Properties);
    }

    public function key(): mixed
    {
        return key($this->Properties);
    }

    #[\ReturnTypeWillChange]
    public function next()
    {
        return next($this->Properties);
    }

    public function valid(): bool
    {
        $key = key($this->Properties);
        return ($key !== NULL && $key !== FALSE);
    }
}