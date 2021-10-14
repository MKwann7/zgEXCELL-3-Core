<?php

namespace App\Utilities\Debug;

class QueryTracker
{
    private $query;
    private $time;
    private $tracking;

    public function __construct($query, $time, $tracking)
    {
        $this->query = $query;
        $this->time = $time;
        $this->tracking = $tracking;
    }

    public function getSeconds()
    {
        return number_format($this->time, 3);
    }

    public function getMiliSeconds()
    {
        return $this->time * 1000;
    }

    public function getTracking()
    {
        return $this->tracking;
    }

    public function getQuery()
    {
        return $this->query;
    }
}
