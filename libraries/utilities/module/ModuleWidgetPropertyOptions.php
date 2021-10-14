<?php
/**
 * Created by PhpStorm.
 * User: micah
 * Date: 6/10/2020
 * Time: 7:36 PM
 */

namespace App\utilities\module;


class ModuleWidgetPropertyOptions
{
    private $options = [];

    public function __construct (array $options)
    {
        $this->options = $options;
    }

    public function getOptions() : array
    {
        return $this->options;
    }
}