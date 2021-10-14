<?php

namespace App\Utilities\Module;

class ModuleWidgetProperty
{
    private $name;
    private $type;
    private $options;
    private $default;
    private $length;

    public function __construct (string $name, string $type, int $length = 250, ModuleWidgetPropertyOptions $options = null, object $default = null)
    {
        if (!in_array($type, ModuleWidgetPropertyType::getTypes(), true))
        {
            throw new Exception("Type {$type} no allowed in ModuleWidgetProperty with {$name} name.");
        }

        $this->name = $name;
        $this->type = $type;
        $this->length = $length;
        $this->options = $options;
        $this->default = $default;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getType() : string
    {
        return $this->type;
    }

    public function getOptions() : string
    {
        return $this->options->getOptions();
    }

    public function getDefault() : object
    {
        return $this->default;
    }

    public function getLength() : int
    {
        return $this->length;
    }
}