<?php

namespace App\Utilities\Excell;

use App\Core\AppModel;
use App\Core\AppEntity;
use Entities\Cards\Models\CardModel;

class ExcellCollection extends ExcellIterator
{
    protected $EntityCount = 0;

    public function GetByIndex($intRowIndex)
    {
        if ( count($this->Properties) === 0 && $intRowIndex >= count($this->Properties))
        {
            return null;
        }

        reset($this->Properties);

        $intPropertyCount = count($this->Properties);

        for($currRowIndex = 0; $currRowIndex < $intPropertyCount; $currRowIndex++)
        {
            if ($currRowIndex === $intRowIndex)
            {
                return current($this->Properties);
            }

            next($this->Properties);
        }
    }

    public function First()
    {
        if ( count($this->Properties) === 0 )
        {
            return null;
        }

        return reset($this->Properties);
    }

    public function Last()
    {
        if ( count($this->Properties) === 0 )
        {
            return null;
        }

        return end($this->Properties);
    }

    public function Count() : int
    {
        if ( count($this->Properties) === 0 )
        {
            return 0;
        }

        return count($this->Properties);
    }

    public function Each($objEntityCallback) : self
    {
        if (! is_callable($objEntityCallback) || count($this->Properties) === 0)
        {
            return $this;
        }

        foreach($this->Properties as $currKey => $currData)
        {
            if (!empty($currData))
            {
                $objEntityCallback($currData, $currKey);
            }
        }

        return $this;
    }

    public function Foreach($objEntityCallback) : self
    {
        if (! is_callable($objEntityCallback) || count($this->Properties) === 0)
        {
            return $this;
        }

        foreach($this->Properties as $currKey => $currData)
        {
            if (!empty($currData))
            {
                $result = $objEntityCallback($currData, $currKey);

                if (empty($result) || $result === false) { continue; }

                $this->Properties[$currKey] = $result;
            }
        }

        return $this;
    }

    public function FindMatching($objEntityCallback) : ExcellCollection
    {
        $collection = new self();
        if (! is_callable($objEntityCallback) || count($this->Properties) === 0)
        {
            return $collection;
        }

        foreach($this->Properties as $currKey => $currData)
        {
            if (!empty($currData))
            {
                $result = $objEntityCallback($currData, $currKey);

                if (empty($result) || $result === false) { continue; }

                $collection->Add($currKey, $result);
            }
        }

        return $collection;
    }

    public function Find($objEntityCallback)
    {
        if (! is_callable($objEntityCallback) || count($this->Properties) === 0)
        {
            return null;
        }

        foreach($this->Properties as $currKey => $currData)
        {
            if (!empty($currData))
            {
                $result = $objEntityCallback($currData, $currKey);

                if (empty($result) || $result === false) { continue; }

                return $result;
            }
        }

        return null;
    }

    public function FindByIndex(int $index)
    {
        $propertyCount = count($this->Properties);

        if ($propertyCount === 0)
        {
            return null;
        }

        $currIndex = 0;

        foreach($this->Properties as $currKey => $currData)
        {
            if ($currIndex === $index) { return $currData; }
            $currIndex++;
        }

        return null;
    }

    public function ReplaceRecord($model, $filters) : self
    {
        if (count($this->Properties) === 0)
        {
            return $this;
        }

        foreach($this->Properties as $currKey => $currData)
        {
            $foundMatch = false;

            foreach($filters as $currFilterField => $currFilterValue)
            {
                if ($currKey === $currFilterField)
                {
                    if ($currFilterValue === $currData)
                    {
                        $foundMatch = true;
                    }
                    else
                    {
                        $foundMatch = false;
                    }
                }
            }

            if ($foundMatch === true)
            {
                $this->Properties[$currKey] = $model;
                return $this;
            }
        }

        return $this;
    }

    public function DeleteByKey($key) : bool
    {
        $propertyCount = count($this->Properties);

        if ($propertyCount === 0)
        {
            return false;
        }

        foreach($this->Properties as $currKey => $currData)
        {
            if ($currKey === $key) { unset($this->Properties[$currKey]); return true; }
        }

        return false;
    }

    public function Filter($objEntityCallback) : self
    {
        if (! is_callable($objEntityCallback) || count($this->Properties) === 0)
        {
            return $this;
        }

        foreach($this->Properties as $currKey => $currData)
        {
            if (!empty($currData))
            {
                $result = $objEntityCallback($currData, $currKey);

                if (empty($result) || $result === false)
                {
                    unset($this->Properties[$currKey]);
                }
                else
                {
                    $this->Properties[$currKey] = $result;
                }
            }
        }

        return $this;
    }

    public function Load($arItems) : self
    {
        if ( !is_array($arItems) || count($arItems) === 0)
        {
            return $this;
        }

        foreach($arItems as $currKey => $currData)
        {
            $this->Add($currData);
        }

        return $this;
    }

    public function HydrateModelData($strModelClass, $blnForceProperties = false) : self
    {
        if (count($this->Properties) === 0)
        {
            return $this;
        }

        foreach($this->Properties as $currKey => $currData)
        {
            $this->Properties[$currKey] = new $strModelClass($currData, $blnForceProperties);
        }

        return $this;
    }

    public function HydrateChildModelData($fieldName, $arUnionFields, $colData, $single = false, $newModel = null) : self
    {
        if((!empty($colData) && !is_a($colData, __CLASS__)) || count($this->Properties) === 0)
        {
            return $this;
        }

        $newModelPattern = new \stdClass();

        if ($newModel !== null && is_array($newModel))
        {
            foreach($newModel as $currKey => $currData)
            {
                $newModelPattern->label = $currKey;
                $newModelPattern->field = $currData;
                break;
            }
        }

        foreach ($this->Properties as $currKey => $objEntityData)
        {
            if ($single === false)
            {
                $this->Properties[$currKey]->AddUnvalidatedValue($fieldName, new ExcellCollection());
            }

            foreach($colData as $currEntity)
            {
                foreach($arUnionFields as $currUnionField => $currRenamedUnionField)
                {
                    if (isInteger($currUnionField))
                    {
                        if (
                            (!empty($currEntity->{$currRenamedUnionField}) && !empty($objEntityData->{$currRenamedUnionField}) ) &&
                            ( $currEntity->{$currRenamedUnionField} == $objEntityData->{$currRenamedUnionField} )
                        )
                        {
                            if ($single === false)
                            {
                                if ($newModel === null)
                                {
                                    $this->Properties[$currKey]->$fieldName->Add($currEntity ?? null);
                                }
                                else
                                {
                                    $this->Properties[$currKey]->$fieldName->Add($currEntity->{$newModelPattern->label}, $currEntity->{$newModelPattern->field} ?? null);
                                }
                            }
                            else
                            {
                                $this->Properties[$currKey]->AddUnvalidatedValue($fieldName,($currEntity ?? null));
                            }
                        }
                    }
                    else
                    {
                        if (
                            (!empty($currEntity->{$currUnionField}) && !empty($objEntityData->{$currRenamedUnionField}) ) &&
                            ( $currEntity->{$currUnionField} == $objEntityData->{$currRenamedUnionField} )
                        )
                        {
                            if ($single === false)
                            {
                                if ($newModel === null)
                                {
                                    $this->Properties[$currKey]->$fieldName->Add($currEntity ?? null);
                                }
                                else
                                {
                                    $this->Properties[$currKey]->$fieldName->Add($currEntity->{$newModelPattern->label}, $currEntity->{$newModelPattern->field} ?? null);
                                }
                            }
                            else
                            {
                                $this->Properties[$currKey]->AddUnvalidatedValue($fieldName, $currEntity);
                            }
                        }
                    }
                }
            }
        }

        return $this;
    }

    public function GetKeyByIndex($intRowIndex)
    {
        if ( count($this->Properties) === 0 && $intRowIndex >= count($this->Properties))
        {
            return null;
        }

        reset($this->Properties);

        $intPropertyCount = count($this->Properties);

        for($currRowIndex = 0; $currRowIndex < $intPropertyCount; $currRowIndex++)
        {
            if ($currRowIndex === $intRowIndex)
            {
                return key($this->Properties);
            }

            next($this->Properties);
        }
    }

    public function FindEntityByKey($strKeyName)
    {
        if ( count($this->Properties) === 0 )
        {
            return null;
        }

        foreach($this->Properties as $currKey => $currData)
        {
            if (!is_a($currData,"stdClass"))
            {
                if (!empty($currData->Properties))
                {
                    if(count($currData->Properties) === 0)
                    {
                        continue;
                    }

                    foreach($currData->Properties as $currChildKey => $currChildData)
                    {
                        if($currChildKey === $strKeyName)
                        {
                            return $currData;
                        }
                    }
                }
            }
            else
            {
                foreach($currData as $currChildKey => $currChildData)
                {
                    if($currChildKey === $strKeyName)
                    {
                        return $currData;
                    }
                }
            }
        }

        return null;
    }

    public function FindEntityByValue($strFieldName, $objValue, $debug = false)
    {
        if ( count($this->Properties) === 0 )
        {
            return null;
        }

        if ($debug === true)
        {
            dump("find {$strFieldName} = {$objValue}");
        }

        foreach($this->Properties as $currKey => $currData)
        {
            if (!is_a($currData,"stdClass"))
            {
                if (!empty($currData->Properties))
                {
                    if(count($currData->Properties) === 0)
                    {
                        continue;
                    }

                    foreach($currData->Properties as $currChildKey => $currChildData)
                    {
                        if($currChildKey == $strFieldName && $currChildData == $objValue)
                        {
                            return $currData;
                        }
                    }
                }
            }
            else
            {
                foreach($currData as $currChildKey => $currChildData)
                {
                    if($currChildKey == $strFieldName && $currChildData == $objValue)
                    {
                        return $currData;
                    }
                }
            }
        }

        return null;
    }

    public function FindEntityByValues(array $fieldsAndValues, $debug = false)
    {
        if ( count($this->Properties) === 0 )
        {
            return null;
        }

        if ($debug === true)
        {
            dump("find {$strFieldName} = {$objValue}");
        }

        foreach($this->Properties as $currKey => $currData)
        {
            if (!is_a($currData,"stdClass"))
            {
                if (!empty($currData->Properties))
                {
                    if(count($currData->Properties) === 0)
                    {
                        continue;
                    }

                    $match = true;

                    foreach($currData->Properties as $currChildKey => $currChildData)
                    {
                        foreach($fieldsAndValues as $currFieldKey => $currValue)
                        {
                            if ($currChildKey == $currFieldKey && $currChildData != $currValue)
                            {
                                $match = false;
                            }
                        }
                    }

                    if ($match === true)
                    {
                        return $currData;
                    }
                }
            }
            else
            {
                $match = true;

                foreach($currData as $currChildKey => $currChildData)
                {
                    foreach($fieldsAndValues as $currFieldKey => $currValue)
                    {
                        if ($currChildKey == $currFieldKey && $currChildData != $currValue)
                        {
                            $match = false;
                        }
                    }
                }

                if ($match === true)
                {
                    return $currData;
                }
            }
        }

        return null;
    }

    public function FindEntityByProperty($strPropertyName, $objValue)
    {
        if ( count($this->Properties) === 0 )
        {
            return null;
        }

        foreach($this->Properties as $currKey => $currData)
        {
            if (!isClass($currData))
            {
                continue;
            }
            else
            {
                if (property_exists(get_class($currData), $strPropertyName))
                {
                    if (strtolower($currData->{$strPropertyName}) === strtolower($objValue))
                    {
                        return $currData;
                    }
                }
            }
        }

        return null;
    }

    public function ReplaceEntityByProperty($strPropertyName, $objValue, $entity) : bool
    {
        if ( count($this->Properties) === 0 )
        {
            return false;
        }

        foreach($this->Properties as $currKey => $currData)
        {
            if (!isClass($currData))
            {
                continue;
            }
            else
            {
                if (property_exists(get_class($currData), $strPropertyName))
                {
                    if (strtolower($currData->{$strPropertyName}) === strtolower($objValue))
                    {
                        $this->Properties[$currKey] = $entity;
                        return true;
                    }
                }
            }
        }

        return false;
    }

    public function DeleteEntityByValue($strFieldName, $objValue) : void
    {
        if ( count($this->Properties) === 0 )
        {
            return;
        }

        foreach($this->Properties as $currKey => $currData)
        {
            if (!is_a($currData,"stdClass"))
            {
                if (!empty($currData->Properties))
                {
                    if(count($currData->Properties) === 0)
                    {
                        continue;
                    }

                    foreach($currData->Properties as $currChildKey => $currChildData)
                    {
                        if($currChildKey === $strFieldName && $currChildData == $objValue)
                        {
                            unset($this->Properties[$currKey]);
                        }
                    }
                }
            }
            else
            {
                foreach($currData as $currChildKey => $currChildData)
                {
                    if($currChildKey === $strFieldName && $currChildData === $objValue)
                    {
                        unset($this->Properties[$currKey]);
                    }
                }
            }
        }

        return;
    }

    public function SortBy($on, $order)
    {
        if (!array($this->Properties) || count($this->Properties) === 0)
        {
            return;
        }

        $new_array = [];
        $sortable_array = [];

        foreach ( $this->Properties as $objEntityKey => $objEntityData )
        {
            // Checks to See if Child is An Array
            if ( is_array($objEntityData) || is_a($objEntityData, "stdClass") || is_a($objEntityData, AppModel::class))
            {
                // Cycles through Child elements, Sorting Begins
                foreach ( $objEntityData as $currEntityFieldKeys => $currEntityFieldValues )
                {
                    // Checks to see if we're using a comparitive array to sort
                    if ($currEntityFieldKeys == $on)
                    {
                        $sortable_array[$objEntityKey] = $currEntityFieldValues;
                    }
                }
            }
            else
            {
                $sortable_array[$objEntityKey] = $objEntityData;
            }
        }

        switch(strtolower($order))
        {
            case 'asc':
            case 'sort_asc':
                asort($sortable_array);
                break;
            case 'desc':
            case 'sort_desc':
                arsort($sortable_array);
                break;
        }

        foreach($sortable_array as $k => $v) {
            $new_array[$k] = $this->Properties[$k];
        }

        $this->Properties = $new_array;
    }

    public function Trim($offset = 0, $length = 1)
    {
        if ( count($this->Properties) === 0 )
        {
            return;
        }

        $intPropertiesCount = 0;
        $arNewProperties = [];

        foreach ($this->Properties as $currKey => $objPropertiesData)
        {
            if ($intPropertiesCount >= $offset && $intPropertiesCount <= ($offset + $length))
            {
                $arNewProperties[] = $objPropertiesData;
            }

            $intPropertiesCount++;
        }

        $this->Properties = $arNewProperties;
    }

    public function CollectionToArray()
    {
        if ( count($this->Properties) === 0 )
        {
            return [];
        }

        $arProperties = [];

        foreach ($this->Properties as $currKey => $objPropertiesData)
        {
            if (is_object($objPropertiesData) && count((array)$objPropertiesData) > 0 )
            {
                foreach ($objPropertiesData as $currChildKey => $objChildPropertiesData)
                {
                    $arProperties[$currKey][$currChildKey] = $objChildPropertiesData;
                }
            }
            else
            {
                $arProperties[$currKey] = $objPropertiesData;
            }
        }

        return $arProperties;
    }

    public function FieldsToArray($arFieldList)
    {
        if ( count($this->Properties) === 0 )
        {
            return [];
        }

        if (empty($arFieldList) || ( !empty($arFieldList) && !is_array($arFieldList)))
        {
            return [];
        }

        $arProperties = [];
        $intArrayIndex = 0;

        foreach ($this->Properties as $currKey => $objPropertiesData)
        {
            $this->ProcessFieldIsolation($arFieldList, $arProperties, $objPropertiesData, $intArrayIndex, $currKey);
        }

        return $arProperties;
    }

    protected function ProcessFieldIsolation($arFieldList, &$arProperties, &$objPropertiesData, &$intArrayIndex, $strKey)
    {
        if (is_object($objPropertiesData) && count((array)$objPropertiesData) > 0 )
        {
            foreach ($objPropertiesData as $currChildKey => $objPropertiesDataValue)
            {
                if (is_a($objPropertiesDataValue, AppModel::class))
                {
                    $this->ProcessFieldIsolation($arFieldList, $arProperties, $objPropertiesDataValue, $intArrayIndex, $strKey);
                }
                else
                {
                    if (array_key_exists($currChildKey, $arFieldList))
                    {
                        if ( count($arFieldList) > 1)
                        {
                            $arrayNewKey = $arFieldList[$currChildKey];
                            $arProperties[$strKey][$arrayNewKey] = $objPropertiesDataValue;
                        }
                        else
                        {
                            $arProperties[] = $objPropertiesDataValue;
                        }
                    }
                    elseif (in_array($currChildKey, $arFieldList))
                    {
                        if ( count($arFieldList) > 1)
                        {
                            $arProperties[$strKey][$currChildKey] = $objPropertiesDataValue;
                        }
                        else
                        {
                            $arProperties[] = $objPropertiesDataValue;
                        }
                    }
                }
            }
        }
        else
        {
            if(in_array($strKey, $arFieldList))
            {
                $arProperties[$intArrayIndex] = $objPropertiesData;
            }
        }
        $intArrayIndex++;
    }

    public function ConcatinateFieldsIntoNew($arConcatinationData) : void
    {
        if(empty($arConcatinationData) || !is_array($arConcatinationData) || count($arConcatinationData) === 0)
        {
            return;
        }

        foreach($arConcatinationData as $currContactNewField => $currConcact)
        {
            if(empty($currConcact[0]) || !is_array($currConcact[0]) || count($currConcact[0]) === 0)
            {
                continue;
            }

            $strConcatinationGlue = " ";

            if(!empty($currConcact[1]))
            {
                $strConcatinationGlue = $currConcact[1];
            }

            foreach($this->Properties as $currKey => $objEntityData)
            {
                $arNewConcatinatedValue = [];

                foreach($objEntityData as $currEntityField => $currEntityValue)
                {
                    foreach($currConcact[0] as $currContactFieldName)
                    {
                        if ($currEntityField != $currContactFieldName || !isset($this->Properties[$currKey]->{$currEntityField}))
                        {
                            continue;
                        }

                        $arNewConcatinatedValue[] = $currEntityValue;
                    }
                }

                $strNewConcatinatedValue = implode($strConcatinationGlue, $arNewConcatinatedValue);
                $this->Properties[$currKey]->AddUnvalidatedValue($currContactNewField, $strNewConcatinatedValue);
            }
        }
    }

    public function AddFieldToAllEntities($fieldName, $value) : self
    {
        foreach ($this->Properties as $currKey => $objEntityData)
        {
            $this->Properties[$currKey]->AddUnvalidatedValue($fieldName, $value ?? null);
        }

        return $this;
    }

    public function RelabelFields($arFieldsToRelabel) : self
    {
        if (count($this->Properties) === 0) { return $this; }

        foreach($arFieldsToRelabel as $existingField => $renamedField)
        {
            foreach ($this->Properties as $currKey => $objEntityData)
            {
                if (!empty($objEntityData->{$existingField}))
                {
                    $this->Properties[$currKey]->AddUnvalidatedValue($renamedField, $objEntityData->{$existingField} ?? null);
                    unset($this->Properties[$currKey]->{$existingField});
                }
            }
        }

        return $this;
    }

    public function MergeFields($objEntity, $arFieldsToMerge, $arUnionFields) : self
    {
        if((!empty($objEntity) && !is_a($objEntity, \App\Utilities\Excell\ExcellCollection::class)) || count($this->Properties) === 0)
        {
            return $this;
        }

        foreach ($this->Properties as $currKey => $objEntityData)
        {
            foreach($objEntity as $currEntity)
            {
                $blnMatchingField = false;
                foreach($arUnionFields as $currUnionField => $currRenamedUnionField)
                {
                    if (isInteger($currUnionField))
                    {
                        if (
                            (!empty($currEntity->{$currRenamedUnionField}) && !empty($objEntityData->{$currRenamedUnionField}) ) &&
                            ( $currEntity->{$currRenamedUnionField} == $objEntityData->{$currRenamedUnionField} )
                        )
                        {
                            $blnMatchingField = true;
                        }
                    }
                    else
                    {
                        if (
                            (!empty($currEntity->{$currUnionField}) && !empty($objEntityData->{$currRenamedUnionField}) ) &&
                            ( $currEntity->{$currUnionField} == $objEntityData->{$currRenamedUnionField} )
                        )
                        {
                            $blnMatchingField = true;
                        }
                    }
                }

                if ($blnMatchingField === true)
                {
                    foreach($arFieldsToMerge as $currMergeField => $currRenamedField)
                    {
                        if (isInteger($currMergeField))
                        {
                            $this->Properties[$currKey]->AddUnvalidatedValue($currRenamedField, $currEntity->{$currRenamedField} ?? null);
                        }
                        else
                        {
                            $this->Properties[$currKey]->AddUnvalidatedValue($currRenamedField, $currEntity->{$currMergeField} ?? null);
                        }
                    }
                }
            }
        }

        return $this;
    }

    public function Merge(ExcellCollection $objDataCollection) : self
    {
        if(empty($objDataCollection) || (!empty($objDataCollection) && !is_a($objDataCollection, \App\Utilities\Excell\ExcellCollection::class)))
        {
            return $this;
        }

        foreach ($objDataCollection as $currKey => $objEntityData)
        {
            $this->Properties[] = $objEntityData;
        }

        return $this;
    }

    public function ConvertDatesToFormat($strDateFormat) : void
    {
        if ( count($this->Properties) === 0 )
        {
            return;
        }

        foreach($this->Properties as $currKey => $objPropertiesData)
        {
            if (!empty($this->Properties[$currKey]->created_on))
            {
                $this->Properties[$currKey]->created_on = date($strDateFormat,strtotime($objPropertiesData->created_on));
            }

            if (!empty($this->Properties[$currKey]->last_updated))
            {
                $this->Properties[$currKey]->last_updated = date($strDateFormat,strtotime($objPropertiesData->last_updated));
            }
        }
    }

    public function MergeWithoutDuplicates(ExcellCollection $objDataCollection, string $field) : self
    {
        if(empty($objDataCollection) || (!empty($objDataCollection) && !is_a($objDataCollection, \App\Utilities\Excell\ExcellCollection::class)))
        {
            return $this;
        }

        foreach ($objDataCollection as $currKey => $objEntityData)
        {
            if (empty($objEntityData->{$field})) { continue; }

            $value = $this->FindEntityByValue($field, $objEntityData->{$field});

            if ($value !== null) { continue; }

            $this->Properties[] = $objEntityData;
        }

        return $this;
    }

    public function AssignCustomFieldsForAdminList(CardModel $objCard) : void
    {
        if ( count($this->Properties) === 0 )
        {
            return;
        }

        foreach($this->Properties as $currKey => $objPropertiesData)
        {
            $cardTabRelData = $this->Properties[$currKey]->card_tab_rel_data;

            $tabColor = $cardTabRelData->Properties->TabCustomColor ?? ($objCard->card_data->style->card->color->main ?? "ff0000");
            $this->Properties[$currKey]->AddUnvalidatedValue("tab_color", $tabColor);
        }
    }

    public function __set($strField, $objValue)
    {
        $this->Properties[$strField] = $objValue;

        return $this;
    }

    public function __get($strName)
    {
        if (isset($this->Properties[$strName]))
        {
            return $this->Properties[$strName];
        }
    }

    public function __isset($strName)
    {
        switch(strtolower($strName))
        {
            case "data":
                return isset($this->Properties);
            default:
                return isset($this->$strName);
        }
    }

    public function __call($strName, $objArguements)
    {
        switch(strtolower($strName))
        {
            case "first":
                return $this->Properties[0];

            case "count":
                return count($this->Properties);
        }

        return $this->$strName($objArguements);
    }

    public function Add()
    {
        $objAllArgs = func_get_args();

        if(count($objAllArgs) === 1)
        {
            $objModelData = $objAllArgs[0];

            $this->Properties[] = $objModelData;
        }

        if(count($objAllArgs) === 2)
        {
            $objModelIndex = $objAllArgs[0];
            $objModelData = $objAllArgs[1];

            $this->Properties[$objModelIndex] = $objModelData;
        }

        return $this;
    }

    public function ConvertToArray($lstProperties = null)
    {
        $arJavaScriptData = [];

        if ($lstProperties === null)
        {
            $lstProperties = $this->Properties;
        }

        foreach($lstProperties as $strKey => $strValue)
        {
            if (is_a($strValue,"stdClass") || is_a($strValue, AppModel::class) || is_array($strValue))
            {
                $arJavaScriptData[$strKey] = $this->ConvertToArray($strValue);
            }
            else
            {
                $arJavaScriptData[$strKey] = $strValue;
            }
        }

        return $arJavaScriptData;
    }

    public function FieldsToJson($arFields = null)
    {
        if($arFields === null)
        {
            return [];
        }

        $arJavaScriptData = $this->ConvertToJson($arFields);
        $strJavaScriptArray = json_encode($arJavaScriptData);

        return $strJavaScriptArray;
    }

    public function ConvertToJson($arFields)
    {
        $arToArray = [];

        foreach ($this->Properties as $currRowIndex => $currEntityRow)
        {
            foreach ($currEntityRow as $currField => $currValue)
            {
                if ($arFields !== null && is_array($arFields))
                {
                    if (!in_array($currField, $arFields))
                    {
                        continue;
                    }
                }

                $arToArray[$currRowIndex][$currField] = $currValue;
            }
        }

        return $arToArray;
    }

    public function ConvertToJavaScriptArray($arFields = null)
    {
        if ( count($this->Properties) === 0 )
        {
            return "[]";
        }

        if($arFields === null)
        {
            return [];
        }

        $arJavaScriptData = $this->ConvertToJavaScript($arFields);
        $strJavaScriptArray = "[" . implode("," . PHP_EOL, $arJavaScriptData) ."]";

        return $strJavaScriptArray;
    }

    public function ConvertToJavaScript($arFields)
    {
        if($arFields === null)
        {
            return [];
        }

        $arJavaScriptData = [];

        foreach($this->Properties as $strKey => $strValue)
        {
            if (!is_a($strValue,"stdClass"))
            {
                $arJavaScriptData[] = $strValue->ConvertToJavaScriptArrayElement($arFields);
            }
            else
            {
                $arJavaScriptData[] = $this->ConvertToJavaScriptStdClassElement($strValue, $arFields);
            }
        }

        return $arJavaScriptData;
    }

    public function ConvertToJavaScriptStdClassElement($objValue, $arFields = null)
    {
        $strJavaScriptArray = "";
        $arJavaScriptData = [];

        foreach($objValue as $strKey => $strValue)
        {
            if (array_search($strKey, $arFields) === false)
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

    public function ToPublicArray($arProperties = null, $collectionKeys = false) : array
    {
        $arToArray = [];
        foreach ($this->Properties as $currField => $currValue)
        {
            if ($arProperties !== null && is_array($arProperties))
            {
                if (is_a($currValue, AppModel::class))
                {
                    $fieldInModel = false;

                    foreach($arProperties as $currProp)
                    {
                        if ($currValue->HasField($currProp))
                        {
                            $fieldInModel = true;
                        }
                    }

                    if ($fieldInModel === false)
                    {
                        continue;
                    }
                }
            }

            if (is_a($currValue, AppModel::class))
            {
                if ($collectionKeys === true)
                {
                    $arToArray[$currField] = $currValue->ToPublicArray($arProperties);
                }
                else
                {
                    $arToArray[] = $currValue->ToPublicArray($arProperties);
                }
            }
            else
            {
                if ($collectionKeys === true)
                {
                    $arToArray[$currField] = $currValue;
                }
                else
                {
                    $arToArray[] = $currValue;
                }
            }
        }

        return $arToArray;
    }
}