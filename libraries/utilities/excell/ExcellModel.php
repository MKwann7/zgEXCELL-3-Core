<?php

namespace App\Utilities\Excell;

use App\Core\AppModel;
use App\Utilities\Database;
use App\Utilities\Transaction\ExcellTransaction;
use Countable;

abstract class ExcellModel extends ExcellIterator
{
    protected $Definitions = array();
    public $Errors = array();

    public function __get($strName)
    {
        if (!isset($this->Properties[$strName]))
        {
            return null;
        }

        return $this->Properties[$strName];
    }

    public function __set($strName, $objValue)
    {
        return $this->Add($strName, $objValue);
    }

    public function __isset($strName)
    {
        return isset($this->Properties[$strName]);
    }

    public function Hydrate($arItems, $force = false) : void
    {
        if ($arItems === null || (!isIterable($arItems) && !$arItems instanceof \Countable) || (is_array($arItems) && count($arItems) === 0))
        {
            return;
        }

        foreach($arItems as $currField => $currValue)
        {
            if ($force === true)
            {
                $this->AddUnvalidatedValue($currField, $currValue);
            }
            else
            {
                $this->Add($currField, $currValue);
            }
        }
    }

    function Add($strName, $objValue) : bool
    {
        if (empty($strName))
        {
            $this->Errors["main"]["setup"] = "Field was not passed in for value assignment.";
            return false;
        }

        if (empty($this->Definitions[$strName]) && strpos($strName, "__") === false)
        {
            $this->Errors["validation"]["integrity"][$strName] = $strName . " is not in the model.";
            return false;
        }

        if ( !$this->ValidateField($strName, $objValue, true) )
        {

            return false;
        }

        $this->Properties[$strName] = $objValue;

        return true;
    }

    public function RemoveField($strField)
    {
        unset($this->Properties[$strField]);

        return $this;
    }

    public function AddUnvalidatedValue($strField, $objValue, $inFront = false, $debug = false)
    {
        if ($inFront === false)
        {
            $this->Properties[$strField] = castValueTypes($objValue, ["no-date"], $debug);
            return $this;
        }

        $this->Properties = [$strField => $objValue] + $this->Properties;

        return $this;
    }

    public function Validate() : bool
    {
        unset($this->Errors["validation"]);

        $this->Errors["validation"] = array();

        foreach ($this->Properties as $currField => $currValue)
        {
            if (!$this->ValidateField($currField, $currValue))
            {
                $this->Errors["validation"]["$currField"] = new ExcellError("Error Validating: " . $currField . " field.", "warning");
            }
        }
        if (count($this->Errors["validation"]) > 0)
        {
            return false;
        }

        return true;
    }

    public function ValidateModel($objDataForValidation) : bool
    {
        if (empty($objDataForValidation) || !is_array($objDataForValidation))
        {
            return false;
        }

        unset($this->Errors["validation"]);

        $this->Errors["validation"] = array();

        foreach ($objDataForValidation as $currField => $currValue)
        {
            if (!$this->ValidateField($currField, $currValue))
            {
                $this->Errors["validation"]["$currField"] = new ExcellError("Error Validating: " . $currField . " field.", "warning");
            }
        }
        if (count($this->Errors["validation"]) > 0)
        {
            return false;
        }

        return true;
    }

    public function HasForeignKey($strName) : bool
    {
        if(empty($this->Definitions[$strName]["fk"]))
        {
            return false;
        }

        return true;
    }

    public function HasField($strName) : bool
    {
        if(empty($this->Definitions[$strName]))
        {
            return false;
        }

        return true;
    }

    public function GetForeignKey($strName) : array
    {
        if(!empty($this->Definitions[$strName]["fk"]))
        {
            return $this->Definitions[$strName]["fk"];
        }

        return array();
    }

    public function ValidateField($strName = null, &$objValue = null, $blnTransformTypes = false)
    {
        $blnModelPassesValidation = true;

        if ( $strName === null && $objValue === null )
        {
            foreach($this->Properties as $currKey => $currPropertyValue)
            {
                if(!$this->ValidateItem($currKey, $currPropertyValue, $blnTransformTypes))
                {
                    $blnModelPassesValidation = false;
                }
            }
        }
        else
        {
            if(!$this->ValidateItem($strName, $objValue, $blnTransformTypes))
            {
                $blnModelPassesValidation = false;
            }
        }

        return $blnModelPassesValidation;
    }

    private function ValidateItem($strName, &$objValue, $blnTransformTypes = false) : bool
    {
        unset($this->Errors[$strName]);

        if (!empty($this->Definitions[$strName]["required"]) && empty($objValue) )
        {
            $this->Errors["validation"][$strName]["required"] = $strName . " is required.";
        }

        if (empty($objValue))
        {
            return true;
        }

        if (($this->Definitions[$strName]["nullable"] ?? false) == true && $objValue === ExcellNull)
        {
            return true;
        }

        if (strpos($strName, "__") !== false)
        {
            return true;
        }

        if (empty($this->Definitions[$strName]))
        {
            return false;
        }

        if (!empty($this->Definitions[$strName]["type"]))
        {
            switch($this->Definitions[$strName]["type"])
            {
                case "int":
                    if (!isInteger($objValue) || $objValue === ExcellNull)
                    {
                        $this->Errors[$strName]["type"] = "The value passed in is not an integer.";
                        return false;
                    }

                    if ( $blnTransformTypes === true) {
                        $objValue = castValueTypes($objValue);
                    }

                    break;

                case "decimal":
                    if (!isDecimal($objValue) || $objValue === ExcellNull)
                    {
                        $this->Errors[$strName]["type"] = "The value passed in  is not a decimal.";
                        return false;
                    }

                    if ( $blnTransformTypes === true) {
                        $objValue = castValueTypes($objValue);
                    }

                    break;

                case "datetime":
                    if (!isDateTime($objValue) || $objValue === ExcellNull)
                    {
                        $this->Errors[$strName]["type"] = $objValue . " is not a datetime.";
                        return false;
                    }

                    // TODO - add $blnTransformTypes qualifier?
                    $objValue = date("Y-m-d H:i:s", strtotime($objValue));

                    break;

                case "string":
                case "varchar":

                    break;

                case "uuid":
                    if (!isUuid($objValue))
                    {
                        $this->Errors[$strName]["type"] = $objValue . " is not a UUID.";
                    }

                    break;

                case "guid":
                    if (!isGuid($objValue))
                    {
                        $this->Errors[$strName]["type"] = $objValue . " is not a GUID.";
                    }

                    break;

                case "json":

                    if (isJson($objValue, $strName) !== true)
                    {
                        return false;
                    }

                    if ( $objValue === ExcellNull )
                    {
                        $objValue = "";
                    }

                    if ( $blnTransformTypes === true && !is_a($objValue,"stdClass") )
                    {
                        $objValue = json_decode($objValue);
                    }

                    break;
                default:

                    if ($objValue === ExcellNull)
                    {
                        $objValue = "";
                    }

                    break;
            }
        }
        else
        {
            // throw an error. This model's definitions isn't setup properly.
        }

        if ( is_string($objValue) &&  !empty($this->Definitions[$strName]["length"]) && floatval($this->Definitions[$strName]["length"]) > 0)
        {
            if ( strlen($objValue) > $this->Definitions[$strName]["length"] )
            {
                $this->Errors[$strName]["length"] = $objValue . " is too long. Limit is " . $this->Definitions[$strName]["length"] . " characters. ".strlen($objValue)." found.";
                return false;
            }
        }

        return true;
    }

    public function getFieldType($strName) : string
    {
        if(empty($this->Definitions[$strName]))
        {
            return "undefined";
        }

        return $this->Definitions[$strName]["type"];
    }

    public function processFieldValuesForDatabaseUpdate()
    {
        unset($this->Errors["validation"]);

        $this->Errors["validation"] = array();

        foreach ($this->Properties as $currField => $currValue)
        {
            if (!$this->ProcessField($currField, $currValue))
            {
                $this->Errors["validation"]["$currField"] = new ExcellError("Error Validating: " . $currField . " field.", "warning");
            }
        }

        if (count($this->Errors["validation"]) > 0)
        {
            return false;
        }

        return true;
    }

    private function ProcessField($strName, &$objValue)
    {
        if (!empty($this->Definitions[$strName]["type"]))
        {
            switch($this->Definitions[$strName]["type"])
            {
                case "int":

                case "decimal":

                    break;

                case "datetime":

                    if (isDateTime($objValue))
                    {
                        $this->Properties[$strName] = date("Y-m-d H:i:s", strtotime($objValue));
                    }

                    break;

                case "json":

                    break;
                default:

                    break;
            }
        }

        if ($strName === "last_updated")
        {
            $this->Properties[$strName] = date("Y-m-d H:i:s");
        }
    }

    public function ConvertDataTypes()
    {
        foreach ($this->Properties as $strName => $currValue)
        {
            if (!empty($this->Definitions[$strName]["type"]))
            {
                switch($this->Definitions[$strName]["type"])
                {
                    case "int":
                    case "decimal":
                    case "datetime":
                        break;

                    case "json":

                        $objDataValueTest = json_decode(json_encode($currValue, JSON_FORCE_OBJECT), true);

                        if ( !empty($objDataValueTest) && (is_array($objDataValueTest) || $objDataValueTest instanceof Countable) && count($objDataValueTest) > 0 )
                        {
                            $objValueTransaction = new ExcellTransaction();
                            $objValueTransaction->Data = $currValue;

                            $this->Properties[$strName] = Database::unBase64Encode($objValueTransaction)->Data;
                        }

                        break;

                    default:

                        break;
                }
            }
        }
    }

    public function ToArray($arProperties = null, $collectionKeys = false)
    {
        $arToArray = [];
        foreach ($this->Properties as $currField => $currValue)
        {
            if ($arProperties !== null && is_array($arProperties))
            {
                if (!in_array($currField, $arProperties) && !array_key_exists($currField, $arProperties))
                {
                    continue;
                }
            }

            $newKey = $currField;

            if ($arProperties !== null && array_key_exists($currField, $arProperties))
            {
                $newKey = $arProperties[$currField];
            }

            if (is_a($currValue, AppModel::class))
            {
                $arToArray[$newKey] = $currValue->ToArray();
            }
            elseif (is_a($currValue, ExcellCollection::class))
            {
                foreach($currValue as $currKey => $currItem)
                {
                    if (is_a($currItem, AppModel::class))
                    {
                        if ($collectionKeys === true)
                        {
                            $arToArray[$newKey][$currKey] = $currItem->ToArray();
                        }
                        else
                        {
                            $arToArray[$newKey][] = $currItem->ToArray();
                        }
                    }
                    else
                    {
                        if ($collectionKeys === true)
                        {
                            $arToArray[$newKey][$currKey] = $currItem;
                        }
                        else
                        {
                            $arToArray[$newKey][] = $currItem;
                        }
                    }
                }
            }
            else
            {
                $arToArray[$newKey] = $currValue;
            }


        }

        return $arToArray;
    }

    public function ToJson()
    {
        return json_encode($this->ToArray());
    }

    public function getErrors()
    {
        return $this->Errors;
    }

    public function Dump()
    {
        // Dumps Contents of Model
    }

    public function ConvertToJavaScriptArrayElement($arFields = null)
    {
        $arJavaScriptData = [];

        foreach($this->Properties as $strKey => $strValue)
        {
            if (array_search($strKey, $arFields) === false && $arFields !== null)
            {
                continue;
            }

            if(isInteger($strValue))
            {
                $arJavaScriptData[] .= $strKey . ":" . $strValue;
            }
            else
            {
                $arJavaScriptData[] .= $strKey . ":\"" . str_replace('"','\"', $strValue) . "\"";
            }
        }

        $strJavaScriptArray = "{" . implode(",", $arJavaScriptData) ."}";

        return $strJavaScriptArray;
    }
}
