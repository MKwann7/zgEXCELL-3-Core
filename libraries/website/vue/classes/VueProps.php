<?php

namespace App\Website\Vue\Classes;

class VueProps
{
    private $name;
    private $type;
    private $value;

    public function __construct (string $name, string $type, string $value)
    {
        if (!in_array(strtolower($type), ["number", "string", "boolean", "array", "object",  "json", "function", "promise"]))
        {
            throw new \Exception("Invalid type: " . $type);
        }

        $this->name = $name;
        $this->type = ucwords(strtolower($type));
        $this->value = $value;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getValue()
    {
        switch(strtolower($this->type))
        {
            case "number":
            case "string":
            case "boolean":
            case "array":
            case "object":
            case "function":
                return $this->value;
            default:
                return json_encode([$this->value]);
        }
    }
}