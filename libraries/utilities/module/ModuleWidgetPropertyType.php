<?php

namespace App\utilities\module;

use ReflectionClass;

class ModuleWidgetPropertyType
{
    const STRING = "string";
    const VARCHAR = "varchar";
    const DATETIME = "datetime";
    const SELECT = "select";
    const BOOLEAN = "boolean";
    const INTEGER = "integer";
    const DECIMAL = "decimal";

    public static function getTypes() : array
    {
        $oClass = new ReflectionClass(__CLASS__);
        return $oClass->getConstants();
    }
}