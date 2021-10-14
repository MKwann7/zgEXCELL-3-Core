<?php

namespace App\Core;

use App\Utilities\Excell\ExcellCollection;
use App\Utilities\Excell\ExcellHttpModel;
use App\Utilities\Excell\ExcellIterator;
use App\Utilities\Excell\ExcellRelationship;
use App\Utilities\Database;
use App\Utilities\Transaction\ExcellTransaction;
use App\Website\Vue\Classes\VueApp;
use App\Website\Vue\Classes\VueModal;
use App\Website\Website;

class AppEntity extends ExcellIterator
{
    public $AppEntity;
    public $strEntityName;
    public $strAliasName;
    public $strDatabaseTable;
    public $strMainModelName;
    public $strMainModelPrimary = "id";
    public $strModelFolder;
    public $strViewFolder;
    public $strSelectedFields = "*";
    private $selecteCountTrue = false;
    public $strDatabaseName = "Main";
    public $Db;
    /** @var App $app */
    protected $app;
    protected $companyId = 0;
    protected $blnFksReplace = false;
    protected $blnGetRelationBindings = false;
    protected $arForeignKeyBindings = array();
    protected $arRelationBindings = array();
    protected $arQueryJoins = array();
    protected $lstAppModels = array();
    protected $lstAppClasses = array();
    protected $lstAppControllers = array();
    protected $lstAppTransaction;
    public $isPrimaryModule = false;

    public function __construct()
    {
        global $app;
        $this->Db = new Database($this->strDatabaseName);
        $this->app = &$app;

        $this->companyId = $this->setCompanyId();
    }

    public function init() : void
    {
        $this->Db = new Database($this->strDatabaseName);
    }

    protected function setCompanyId() : int
    {
        if ($this->app->objCustomPlatform === null)
        {
            return 0;
        }

        return $this->app->objCustomPlatform->getCompanyId();
    }

    public static function getAlias()
    {
        return (new static)->strAliasName;
    }

    public function getModuleFolder(array $objModuleData) : string
    {
        if (empty($objModuleData["Main"]["Folders"]["Module"]))
        {
            throw new \Exception("This request cannot be completed: Error Code: 374561.");
        }
        return $objModuleData["Main"]["Folders"]["Module"];
    }

    public function getModuleName(array $objModuleData) : string
    {
        if (empty($objModuleData["Main"]["Name"]))
        {
            throw new \Exception("This request cannot be completed: Error Code: 794632.");
        }
        return $objModuleData["Main"]["Name"];
    }

    public function getById($intEntityRowId) : ExcellTransaction
    {
        if (empty($this->strEntityName) || empty($this->strMainModelName))
        {
            $this->lstAppTransaction = new ExcellTransaction();

            $this->lstAppTransaction->Result->Success = false;
            $this->lstAppTransaction->Result->Count = 0;
            $this->lstAppTransaction->Result->Message = "This module isn't setup correctly. Error #6934582";

            return $this->lstAppTransaction;
        }

        if (!isset($intEntityRowId) || $intEntityRowId === null)
        {
            $this->lstAppTransaction = new ExcellTransaction();

            $this->lstAppTransaction->Result->Success = false;
            $this->lstAppTransaction->Result->Count = 0;
            $this->lstAppTransaction->Result->Message = "You must pass in an id to retrieve a " . $this->strMainModelName . " row.";

            return $this->lstAppTransaction;
        }

        /** @var AppModel $objEntityModel */
        $objEntityModel = new $this->strMainModelName();

        if(!$objEntityModel->Add($this->strMainModelPrimary, $intEntityRowId))
        {
            $this->lstAppTransaction = new ExcellTransaction();

            $this->lstAppTransaction->Result->Success = false;
            $this->lstAppTransaction->Result->Count = 0;
            $this->lstAppTransaction->Result->Message = "The value passed in for " . $this->strMainModelPrimary ." did not pass validation: " . $intEntityRowId;
            $this->lstAppTransaction->Result->Errors = $objEntityModel->Errors;

            return $this->lstAppTransaction;
        }

        return $this->getWhere([$this->strMainModelPrimary => $intEntityRowId], 1);
    }

    public function getBySysRowId($sysRowId) : ExcellTransaction
    {
        if (empty($this->strEntityName) || empty($this->strMainModelName))
        {
            $this->lstAppTransaction = new ExcellTransaction();

            $this->lstAppTransaction->Result->Success = false;
            $this->lstAppTransaction->Result->Count = 0;
            $this->lstAppTransaction->Result->Message = "This module isn't setup correctly. Error #6934582";

            return $this->lstAppTransaction;
        }

        if (!isset($sysRowId) || $sysRowId === null)
        {
            $this->lstAppTransaction = new ExcellTransaction();

            $this->lstAppTransaction->Result->Success = false;
            $this->lstAppTransaction->Result->Count = 0;
            $this->lstAppTransaction->Result->Message = "You must pass in an id to retrieve a " . $this->strMainModelName . " row.";

            return $this->lstAppTransaction;
        }

        /** @var AppModel $objEntityModel */
        $objEntityModel = new $this->strMainModelName();

        if(!$objEntityModel->Add("sys_row_id", $sysRowId))
        {
            $this->lstAppTransaction = new ExcellTransaction();

            $this->lstAppTransaction->Result->Success = false;
            $this->lstAppTransaction->Result->Count = 0;
            $this->lstAppTransaction->Result->Message = "The value passed in for the SysRowId did not pass validation: " . $sysRowId;
            $this->lstAppTransaction->Result->Errors = $objEntityModel->Errors;

            return $this->lstAppTransaction;
        }

        return $this->getWhere(["sys_row_id" => $sysRowId], 1);
    }

    public function getFks($arForeignKeyBindings = null) : self
    {
        $this->blnFksReplace = true;

        if (is_array($arForeignKeyBindings) && count($arForeignKeyBindings) > 0)
        {
            $this->arForeignKeyBindings = $arForeignKeyBindings;
        }

        return $this;
    }

    public function getRelations($arRelationBindings = null) : self
    {
        $this->blnGetRelationBindings = true;

        if (is_array($arRelationBindings) && count($arRelationBindings) > 0)
        {
            $this->arRelationBindings = $arRelationBindings;
        }

        return $this;
    }


    public function selectFields($arSelectedFields = null) : self
    {
        if (is_array($arSelectedFields) && count($arSelectedFields) > 0)
        {
            foreach($arSelectedFields as $currSelectedKey => $currSelectedField)
            {
                $arSelectedFields[$currSelectedKey] = $this->strDatabaseTable . "." . $currSelectedField;
            }

            $this->strSelectedFields = implode(", " , $arSelectedFields);
        }

        return $this;
    }

    protected function buildRelationshipModel($label, $database, $table, $field, $foreignKey, $localKey, $arAdditionalBindings = null) : ExcellRelationship
    {
        $objNewRelationship = new ExcellRelationship();

        $objNewRelationship->Label = $label;
        $objNewRelationship->Database = $database;
        $objNewRelationship->Table = $table;
        $objNewRelationship->Field = $field;
        $objNewRelationship->ForeignKey = $foreignKey;
        $objNewRelationship->LocalKey = $localKey;
        $objNewRelationship->AdditionalBindings = $arAdditionalBindings;

        return $objNewRelationship;
    }

    public function noFks()
    {
        $this->blnFksReplace = false;

        $this->arForeignKeyBindings = null;

        return $this;
    }

    public function disableFksTemporarily(&$blnTempRengageFksFlag) : void
    {
        if ($this->blnFksReplace === true)
        {
            $blnTempRengageFksFlag = true;
            $this->blnFksReplace = false;
        }
    }

    public function renableFksIfTemporarilyDisabled(&$blnTempRengageFksFlag) : void
    {
        if ($blnTempRengageFksFlag === true)
        {
            $this->blnFksReplace = true;
        }
    }

    public function getAllRows() : ExcellTransaction
    {
        $objTransaction = new ExcellTransaction();

        if ( empty($this->strEntityName) || empty($this->strMainModelName) )
        {
            $objTransaction->Result->Success = false;
            $objTransaction->Result->Count = 0;
            $objTransaction->Result->Message = "This model is not setup correctly.";

            return $objTransaction;
        }

        $strSelectClause = "*";

        if ($this->blnFksReplace === true)
        {
            //$strJoinClause = $this->ProcessJoinClauseRequest();
            $strSelectClauseCollection = $this->processSelectClauseRequest();

            if ( !empty($strSelectClauseCollection))
            {
                $strSelectClause = $this->strDatabaseTable . ".*, " . $this->processSelectClauseRequest();
            }
        }

        $strGetEntityByWhereClauseQuery = "SELECT $strSelectClause FROM `" . $this->strDatabaseTable . "`";

        $this->init();
        $objEntityResult = $this->Db->getComplex($strGetEntityByWhereClauseQuery,null,null, $this->strMainModelPrimary);

        if ( $objEntityResult->Result->Success === false || $objEntityResult->Result->Count === 0 )
        {
            return $objEntityResult;
        }

        foreach($objEntityResult->Data as $currEntityIndex => $currEntityResult)
        {
            $objValidatedModel = $this->buildModel($currEntityResult);

            $objEntityResult->Data->{$currEntityIndex} = $objValidatedModel->Data;
        }

        $objTransaction->Result->Success = true;
        $objTransaction->Result->Count = $objEntityResult->Result->Count;
        $objTransaction->Result->Message = "This query returned " . $objEntityResult->Result->Count . " Results.";
        $objTransaction->Data = $objEntityResult->Data;

        $this->noFks();

        return $objTransaction;
    }

    public function leftJoin($arJoin) : self
    {
        $this->arQueryJoins["LeftJoin"][] = $arJoin;

        return $this;
    }

    protected function processLeftJoin($arJoins) : string
    {
        if (!is_array($arJoins) || count($arJoins) === 0)
        {
            return "";
        }

        $strLeftJoin = "";

        foreach($arJoins as $arJoin)
        {
            foreach($arJoin as $currEntityName => $currModuleData)
            {
                $objModule = new $currEntityName();

                if (empty($objModule))
                {
                    continue;
                }

                $strLeftJoin .= "LEFT JOIN `" . $objModule->strDatabaseTable . "` ON " . $objModule->strDatabaseTable . "." . $currModuleData["source-fk"] . " = main." . $currModuleData["target-fk"] . " ";
            }
        }

        return $strLeftJoin;
    }

    public function getAll($pageCount = 100000, $pageIndex = 1) : ExcellTransaction
    {
        $intOffset = ($pageIndex - 1) * $pageCount;
        return $this->getWhere(null, [$intOffset, $pageCount]);
    }

    public function getBatchWhere($params = null, $pageCount = 100000, $pageIndex = 1) : ExcellTransaction
    {
        $intOffset = ($pageIndex - 1) * $pageCount;
        return $this->getWhere($params, [$intOffset, $pageCount]);
    }

    public function getCountWhere($params = null, $pageCount = 100000, $pageIndex = 1) : ExcellTransaction
    {
        $this->selecteCountTrue = true;
        $args = func_get_args();
        return call_user_func_array("self::getWhere", $args);
    }

    private function loadCustomSelection() : string
    {
        $strSelectClause = $this->strSelectedFields;

        if ($this->blnFksReplace === true)
        {
            //$strJoinClause = $this->ProcessJoinClauseRequest();
            $strSelectClauseCollection = $this->processSelectClauseRequest();

            if ( !empty($strSelectClauseCollection))
            {
                $strSelectClause = ($this->strSelectedFields == "*" ? $this->strDatabaseTable . ".*" : $this->strSelectedFields) . ", " . $strSelectClauseCollection;
            }
        }

        if ($this->blnGetRelationBindings === true)
        {
            //$strJoinClause = $this->ProcessJoinClauseRequest();
            $strRelationshipCollection = $this->processRelationshipRequest();

            if ( !empty($strRelationshipCollection))
            {
                if ($strSelectClause != $this->strSelectedFields)
                {
                    $strSelectClause = $strSelectClause . ", " . $strRelationshipCollection;
                }
                else
                {
                    $strSelectClause = $this->strDatabaseTable . ".*, " . $strRelationshipCollection;
                }
            }
        }

        return $strSelectClause;
    }

    public function getWhere() : ExcellTransaction
    {
        $objTransaction = new ExcellTransaction();

        if ( empty($this->strEntityName) || empty($this->strMainModelName) )
        {
            $objTransaction->Result->Success = false;
            $objTransaction->Result->Count = 0;
            $objTransaction->Result->Message = "This model is not setup correctly.";

            return $objTransaction;
        }

        $objAllArgs = func_get_args();

        $objWhereClauseRequest = self::assembleWhereClauseArray($objAllArgs);

        if ( $objWhereClauseRequest->Result->Success === false)
        {
            return $objWhereClauseRequest;
        }

        $objWhereClause = $this->processWhereClauseRequest($objWhereClauseRequest->Data["WhereClause"]);

        $objWhereSortByFilter = $objWhereClauseRequest->Data["SortBy"];
        $objWhereClauseLimit = $objWhereClauseRequest->Data["Limit"];

        if ($objWhereClause->Result->Success === false)
        {
            return $objWhereClause;
        }

        $strWhereClause = !empty($objWhereClause->Data) ? " WHERE " . $objWhereClause->Data : "";

        $strSelectClause = "COUNT(*)";

        if  ($this->selecteCountTrue === false)
        {
            $strSelectClause = $this->loadCustomSelection();
        }

        $strLeftJoin = "";
        $strMainDatabaseTable = "`" . $this->strDatabaseTable . "`";

        if (!empty($this->arQueryJoins["LeftJoin"]) && is_array($this->arQueryJoins["LeftJoin"]) && count($this->arQueryJoins["LeftJoin"]) > 0)
        {
            $strLeftJoin = " ".$this->processLeftJoin($this->arQueryJoins["LeftJoin"]). " ";
            $strMainDatabaseTable = $strMainDatabaseTable . " main";
        }

        $strGetEntityByWhereClauseQuery = "SELECT $strSelectClause FROM " . $strMainDatabaseTable . " " . $strLeftJoin . $strWhereClause . " " . $objWhereSortByFilter . " " . $objWhereClauseLimit;

        $this->init();
        $objEntityResult = $this->Db->getComplex($strGetEntityByWhereClauseQuery,null,null, $this->strMainModelPrimary);

        if ( $objEntityResult->Result->Success === false || $objEntityResult->Result->Count === 0 )
        {
            return $objEntityResult;
        }

        if ($this->selecteCountTrue === true)
        {
            $count = new \stdClass();
            $count->count = $objEntityResult->Data->First()->{"COUNT(*)"};
            $objTransaction->Result->Success = true;
            $objTransaction->Result->Count = $count->count;
            $objTransaction->Result->Message = "This query returned " . $count->count . " Results.";
            $objTransaction->Result->Query = $strGetEntityByWhereClauseQuery;
            $objTransaction->Data = $count;

            $this->noFks();
            $this->selecteCountTrue = false;

            return $objTransaction;
        }

        foreach($objEntityResult->Data as $currEntityIndex => $currEntityResult)
        {
            $objValidatedModel = $this->buildModel($currEntityResult);
            $objValidatedModel->Data->ConvertDataTypes();
            $objEntityResult->Data->{$currEntityIndex} = $objValidatedModel->Data;

            if (!empty($strLeftJoin))
            {
                foreach($this->arQueryJoins["LeftJoin"] as $arJoin)
                {
                    foreach($arJoin as $currEntityName => $currModuleData)
                    {
                        /** @var AppEntity $objModule */
                        $objModule = new $currEntityName();

                        if (empty($objModule))
                        {
                            continue;
                        }

                        $objJoinedValidatedModel = $objModule->buildModel($currEntityResult);

                        $objEntityResult->Data->{$currEntityIndex}->AddUnvalidatedValue( $objModule->strMainModelName, $objJoinedValidatedModel->Data);
                    }
                }
            }
        }

        $objTransaction->Result->Success = true;
        $objTransaction->Result->Count = $objEntityResult->Result->Count;
        $objTransaction->Result->Message = "This query returned " . $objEntityResult->Result->Count . " Results.";
        $objTransaction->Result->Query = $strGetEntityByWhereClauseQuery;
        $objTransaction->Data = $objEntityResult->Data;

        $this->noFks();

        return $objTransaction;
    }

    public function getWhereFkv() : ExcellTransaction
    {
        $objTransaction = new ExcellTransaction();

        if ( empty($this->strEntityName) || empty($this->strMainModelName) )
        {
            $objTransaction->Result->Success = false;
            $objTransaction->Result->Count = 0;
            $objTransaction->Result->Message = "This model is not setup correctly.";

            return $objTransaction;
        }

        $objAllArgs = func_get_args();

        if(empty($objAllArgs[0]) || !is_array($objAllArgs[0]))
        {
            $objTransaction->Result->Success = false;
            $objTransaction->Result->Count = 0;
            $objTransaction->Result->Message = "No request array was provided to parse.";

            return $objTransaction;
        }

        $objWhereClauseRequest = self::assembleWhereClauseArray($objAllArgs);

        if ( $objWhereClauseRequest->Result->Success === false)
        {
            return $objWhereClauseRequest;
        }

        $objWhereSortByFilter = $objWhereClauseRequest->Data["SortBy"];
        $objWhereClauseLimit = $objWhereClauseRequest->Data["Limit"];

        /** @var AppModel $objNewModel */
        $objNewModel = new $this->strMainModelName();

        $arJoinClause = [];

        foreach($objAllArgs[0] as $strModelField => $strForeignKeyValue)
        {
            if ($objNewModel->HasForeignKey($strModelField))
            {
                $arForeignKeyBinding = $objNewModel->GetForeignKey($strModelField);
                $arJoinClause[] = $this->strDatabaseTable . "." .$strModelField ." = (SELECT ". $arForeignKeyBinding["key"] ." FROM `" .
                    $arForeignKeyBinding["table"] .
                    "` WHERE " .
                    $arForeignKeyBinding["table"] . "." . $arForeignKeyBinding["key"] . " = " . $this->strDatabaseTable . "." . $strModelField .
                    " AND " . $arForeignKeyBinding["table"] . "." . $arForeignKeyBinding["value"] . " = '" . $strForeignKeyValue . "'" .
                    ")";
            }
        }

        $strSelectClause = implode(" AND ", $arJoinClause);

        $strGetEntityByWhereClauseQuery = "SELECT * FROM `" . $this->strDatabaseTable . "` WHERE " . $strSelectClause . " " . $objWhereSortByFilter . " " . $objWhereClauseLimit;

        $this->init();
        $objEntityResult = $this->Db->getComplex($strGetEntityByWhereClauseQuery,null,null, $this->strMainModelPrimary);

        if ( $objEntityResult->Result->Success === false || $objEntityResult->Result->Count === 0 )
        {
            return $objEntityResult;
        }

        foreach($objEntityResult->Data as $currEntityIndex => $currEntityResult)
        {
            $objValidatedModel = $this->buildModel($currEntityResult);

            $objValidatedModel->Data->ConvertDataTypes();

            $objEntityResult->Data->{$currEntityIndex} = $objValidatedModel->Data;
        }

        $objTransaction->Result->Success = true;
        $objTransaction->Result->Count = $objEntityResult->Result->Count;
        $objTransaction->Result->Message = "This query returned " . $objEntityResult->Result->Count . " Results.";
        $objTransaction->Result->Query = $strGetEntityByWhereClauseQuery;
        $objTransaction->Data = $objEntityResult->Data;

        $this->noFks();

        return $objTransaction;
    }

    public function getWhereIn($field, $values, $sortBy = null, $limit = null) : ExcellTransaction
    {
        $objTransaction = new ExcellTransaction();

        if (!empty($values) && !is_array($values))
        {
            $objTransaction->Result->Success = false;
            $objTransaction->Result->Count = 0;
            $objTransaction->Result->Message = "You must include an array as a second parameter for the getWhereIn method to work.";

            return $objTransaction;
        }

        return $this->getWhere([$field, "IN", $values], $sortBy, $limit);
    }

    protected function processJoinClauseRequest() : string
    {
        $arJoinClause = array();

        $objNewModel = $this->app->GetModel($this->strEntityName, $this->strMainModelName);

        $intJoinIndex = 1;

        foreach($objNewModel as $strModelField => $objModelValue)
        {
            if ($objNewModel->HasForeignKey($strModelField))
            {
                $arForeignKeyBinding = $objNewModel->GetForeignKey($strModelField);
                $arJoinClause[] = "LEFT JOIN `" .
                    $arForeignKeyBinding["table"] .
                    "` AS a$intJoinIndex ON " .
                    $arForeignKeyBinding["table"] . "." . $arForeignKeyBinding["key"] .
                    " = " . $this->strDatabaseTable . "." . $strModelField;

                $intJoinIndex ++;
            }
        }

        $strJoinClause = implode(", ", $arJoinClause);

        return $strJoinClause;
    }

    protected function processSelectClauseRequest() : string
    {
        $arJoinClause = array();

        /** @var AppModel $objNewModel */
        $objNewModel = new $this->strMainModelName();

        $intJoinIndex = 1;

        foreach($objNewModel->getDefinitions() as $strModelField => $objModelValue)
        {
            if ($objNewModel->HasForeignKey($strModelField))
            {
                if ( !empty($this->arForeignKeyBindings) && !in_array($strModelField, $this->arForeignKeyBindings))
                {
                    continue;
                }

                $strDatabase = (!empty($arForeignKeyBinding["db"]) ? $this->app->objDBs->{$arForeignKeyBinding["db"]} : $this->app->objDBs->{$this->strDatabaseName})->Database;

                $arForeignKeyBinding = $objNewModel->GetForeignKey($strModelField);
                $arJoinClause[] = "(SELECT ".$arForeignKeyBinding["value"]." FROM `{$strDatabase}`.`" .
                    $arForeignKeyBinding["table"] .
                    "` WHERE " .
                    $arForeignKeyBinding["table"] . "." . $arForeignKeyBinding["key"] .
                    " = " . $this->strDatabaseTable . "." . $strModelField . " LIMIT 1)" .
                    " AS " . $strModelField . "__value";

                $intJoinIndex++;
            }
        }

        $strJoinClause = implode(", ", $arJoinClause);

        return $strJoinClause;
    }

    protected function processRelationshipRequest() : string
    {
        if (empty($this->arRelationBindings) || count($this->arRelationBindings) === 0)
        {
            return "";
        }

        $arJoinClause = array();

        foreach($this->arRelationBindings as $currRelationship)
        {
            $self = $this;
            $strRelMethod = $currRelationship . "Relationship";

            if (!method_exists($self, $strRelMethod))
            {
                continue;
            }

            /** @var ExcellRelationship $objRelationship */
            $objRelationship = $self->$strRelMethod();

            $strDatabase = (!empty($objRelationship->Database) ? $this->app->objDBs->{$objRelationship->Database} : $this->app->objDBs->{$this->strDatabaseName})->Database;

            $strJoinString = "(SELECT ";

            if (is_array($objRelationship->Field))
            {
                if (count($objRelationship->Field) > 1)
                {
                    $arConcatFields = [];
                    foreach($objRelationship->Field as $currConcatField)
                    {
                        $arConcatFields[] = $currConcatField;
                    }

                    $strJoinString .= "CONCAT_WS(' ', " . implode(", ", $arConcatFields) . ")";
                }
                else
                {
                    $strJoinString .= $objRelationship->Field[0];
                }
            }
            else
            {
                if (strtolower($objRelationship->Field) === "count()")
                {
                    $strJoinString .= "COUNT(*)";
                }
                else
                {
                    $strJoinString .= $objRelationship->Field;
                }
            }

            $strJoinString .= " FROM `{$strDatabase}`.`{$objRelationship->Table}`" .
                " WHERE " .
                $objRelationship->Table . "." . $objRelationship->ForeignKey .
                " = " . $this->strDatabaseTable . "." . $objRelationship->LocalKey;

            if (!empty($objRelationship->AdditionalBindings) && is_array($objRelationship->AdditionalBindings) && count($objRelationship->AdditionalBindings) > 0)
            {
                $arAdditionaBindings = [];
                foreach($objRelationship->AdditionalBindings as $currAdditionalBindingForeignKey => $currAdditionalBindingForeignValue)
                {
                    if (isInteger($currAdditionalBindingForeignValue))
                    {
                        $arAdditionaBindings[] = " `{$currAdditionalBindingForeignKey}` = {$currAdditionalBindingForeignValue}";
                    }
                    else
                    {
                        $arAdditionaBindings[] = " `{$currAdditionalBindingForeignKey}` = '{$currAdditionalBindingForeignValue}'";
                    }
                }

                $strJoinString .= " AND " . implode(" AND ", $arAdditionaBindings);
            }

            $strJoinString .= " ORDER BY " . $objRelationship->Table . "." . $objRelationship->ForeignKey . " DESC LIMIT 1)" .
                " AS " . $objRelationship->Label ."__binding";

            $arJoinClause[] = $strJoinString;
        }

        $strJoinClause = implode(", ", $arJoinClause);

        return $strJoinClause;
    }

    protected function assembleWhereClauseArray($objAllArgs) : ExcellTransaction
    {
        $objReturnTransaction = new ExcellTransaction();

        $objWhereClauseRequest = array();
        $objWhereSortByFilter = "";
        $objWhereClauseLimit = "";

        if ((!empty($objAllArgs[0]) && is_array($objAllArgs[0])) || empty($objAllArgs[0]))
        {
            $objWhereClauseRequest = null;

            if (!empty($objAllArgs[0]))
            {
                $objWhereClauseRequest = array($objAllArgs[0]);
            }

            if ( !empty($objAllArgs[1]) && is_numeric($objAllArgs[1]) )
            {
                $objWhereClauseLimit = "LIMIT " . $objAllArgs[1];
            }
            elseif ( !empty($objAllArgs[1]) && is_array($objAllArgs[1]) && count($objAllArgs[1]) === 2 )
            {
                $objWhereClauseLimit = "LIMIT " . $objAllArgs[1][0] . ", " . $objAllArgs[1][1];
            }
            elseif ( !empty($objAllArgs[1]) && is_string($objAllArgs[1]) )
            {
                $strOrderBy = "";
                $objWhereSortByFilter = "ORDER BY `" . $objAllArgs[1] . "`";

                if (strpos($objAllArgs[1],".") !== false)
                {
                    $arOrderBy = explode(".", $objAllArgs[1]);
                    $objWhereSortByFilter = "ORDER BY `" . $arOrderBy[0] . "` {$arOrderBy[1]}";
                }

                if ( !empty($objAllArgs[2]) && is_numeric($objAllArgs[2]) )
                {
                    $objWhereClauseLimit = "LIMIT " . $objAllArgs[2];
                }
                elseif ( !empty($objAllArgs[2]) && is_array($objAllArgs[2]) && count($objAllArgs[2]) === 2 )
                {
                    $objWhereClauseLimit = "LIMIT " . $objAllArgs[2][0] . ", " . $objAllArgs[2][1];
                }
            }
        }
        else
        {
            if ( count($objAllArgs) === 3 )
            {
                $objWhereClauseRequest = array(array($objAllArgs[0], $objAllArgs[1], $objAllArgs[2]));
            }
            elseif ( count($objAllArgs) === 2 )
            {
                $objWhereClauseRequest = array(array($objAllArgs[0], "=", $objAllArgs[1]));
            }
            else
            {
                $objReturnTransaction->Result->Success = false;
                $objReturnTransaction->Result->Count = 0;
                $objReturnTransaction->Result->Message = "This where clause request was not is not setup correctly: " . json_encode($objAllArgs);
            }
        }

        $objReturnTransaction->Result->Success = true;
        $objReturnTransaction->Result->Count = 1;
        $objReturnTransaction->Data = array("WhereClause" => $objWhereClauseRequest, "SortBy" => $objWhereSortByFilter, "Limit" => $objWhereClauseLimit);

        return $objReturnTransaction;
    }

    protected function assembleUpdateString($objModel) : ExcellTransaction
    {
        $objReturnTransaction = new ExcellTransaction();

        $arUpdateFields = array();

        $objModel->processFieldValuesForDatabaseUpdate();

        foreach($objModel as $currFieldName => $currFieldValue)
        {
            if (!empty($currFieldValue) && $currFieldName !== "created_on" && $currFieldName !== "sys_row_id" && $currFieldName != $this->strMainModelPrimary && strpos($currFieldName, "__value") === false)
            {
                $strUpdateFieldValue = "`" . $currFieldName . "` = ";

                if ( $currFieldValue === ExcellNull)
                {
                    $strUpdateFieldValue .= 'null';
                }
                elseif ( $currFieldValue === ExcellTrue)
                {
                    $strUpdateFieldValue .= 'TRUE';
                }
                elseif ( $currFieldValue === ExcellFalse)
                {
                    $strUpdateFieldValue .= 'FALSE';
                }
                elseif ( $currFieldValue === ExcellEmptyString)
                {
                    $strUpdateFieldValue .= "''";
                }
                elseif ( $currFieldValue === "last_updated")
                {
                    $strUpdateFieldValue .= "'" . date("Y-m-d\TH:i:s") . "'";
                }
                elseif ( isDecimal($currFieldValue) || isInteger($currFieldValue))
                {
                    $strUpdateFieldValue .= $currFieldValue;
                }
                elseif ( is_a($currFieldValue, ExcellCollection::class))
                {
                    continue;
                }
                else
                {
                    if ($objModel->getFieldType($currFieldName) === "string" || $objModel->getFieldType($currFieldName) === "varchar")
                    {
                        $currFieldValue = Database::forceUtf8(str_replace("'", "\'", str_replace("\'", "'", str_replace('&#39;', "'",$currFieldValue))));
                    }

                    if ($objModel->getFieldType($currFieldName) === "json")
                    {
                        if (isJson($currFieldValue) === true)
                        {
                            if (is_object($currFieldValue) && is_a($currFieldValue,"stdClass"))
                            {
                                $currFieldValue = json_decode(json_encode($currFieldValue, JSON_FORCE_OBJECT), true);
                            }
                            elseif (!is_array($currFieldValue))
                            {
                                $currFieldValue =  json_decode($currFieldValue, true);
                            }
                        }

                        $objValueTransaction = new ExcellTransaction();
                        $objValueTransaction->Data = $currFieldValue;

                        $currFieldValue = json_encode(Database::base64Encode($objValueTransaction)->Data);
                    }

                    if (is_array($currFieldValue))
                    {
                        $currFieldValue = json_encode($currFieldValue);
                    }

                    $strUpdateFieldValue .= "'" . $currFieldValue . "'";
                }

                $arUpdateFields[] = $strUpdateFieldValue;
            }
        }

        $objReturnTransaction->Data->{0} = implode(", ", $arUpdateFields);

        $objReturnTransaction->Result->Success = true;
        $objReturnTransaction->Result->Count = 1;

        return $objReturnTransaction;
    }

    protected function assembleInsertionStrings($objModel) : ExcellTransaction
    {
        $objReturnTransaction = new ExcellTransaction();

        $arInsertionFields = array();
        $arInsertionValues = array();

        foreach($objModel as $currFieldName => $currFieldValue)
        {
            $arInsertionFields[] = "`" . $currFieldName . "`";
            if ( $currFieldName === "created_on")
            {
                $arInsertionValues[] = "'".date("Y-m-d H:i:s")."'";
            }
            elseif ( $currFieldValue === null)
            {
                $arInsertionValues[] = 'null';
            }
            elseif ( $currFieldValue === ExcellTrue)
            {
                $arInsertionValues[] = 'TRUE';
            }
            elseif ( $currFieldValue === ExcellFalse)
            {
                $arInsertionValues[] = 'FALSE';
            }
            elseif ( $currFieldValue === ExcellEmptyString)
            {
                $arInsertionValues[] = "''";
            }
            elseif ( $currFieldValue === "last_updated" || $currFieldValue === "created_on" )
            {
                $arInsertionValues[] = "'" . date("Y-m-d\TH:i:s") . "'";
            }
            elseif ( isDecimal($currFieldValue) || isInteger($currFieldValue))
            {
                $arInsertionValues[] = $currFieldValue;
            }
            else
            {
                if ($objModel->getFieldType($currFieldName) === "string" || $objModel->getFieldType($currFieldName) === "varchar")
                {
                    $currFieldValue = str_replace("'", "\'", str_replace("\'", "'", str_replace('&#39;', "'",$currFieldValue)));
                }

                if ($objModel->getFieldType($currFieldName) === "json")
                {
                    if (isJson($currFieldValue) === true)
                    {
                        if (is_object($currFieldValue) && is_a($currFieldValue,"stdClass"))
                        {
                            $currFieldValue = json_decode(json_encode($currFieldValue, JSON_FORCE_OBJECT), true);
                        }
                        elseif (!is_array($currFieldValue))
                        {
                            $currFieldValue =  json_decode($currFieldValue, true);
                        }
                    }

                    $objValueTransaction = new ExcellTransaction();
                    $objValueTransaction->Data = $currFieldValue;

                    $currFieldValue = json_encode(Database::base64Encode($objValueTransaction)->Data);
                }

                $arInsertionValues[] = "'" . $currFieldValue . "'";
            }
        }

        $objReturnTransaction->Data->{0} = implode(", ", $arInsertionFields);
        $objReturnTransaction->Data->{1} = implode(", ", $arInsertionValues);

        $objReturnTransaction->Result->Success = true;
        $objReturnTransaction->Result->Count = 2;

        return $objReturnTransaction;
    }

    protected function getNextId() : int
    {
        $this->init();
        return $this->Db->getNextEntityId($this->strDatabaseTable, $this->strMainModelPrimary);
    }

    public function createNew($objEntityData) : ExcellTransaction
    {
        if (!is_subclass_of($objEntityData, AppModel::class))
        {
            $this->lstAppTransaction = new ExcellTransaction();

            $this->lstAppTransaction->Result->Success = false;
            $this->lstAppTransaction->Result->Count = 0;
            $this->lstAppTransaction->Result->Message = "You must pass in a data model.";

            return $this->lstAppTransaction;
        }

        $objEntityModelResult = $this->buildModel($objEntityData);

        if ( $objEntityModelResult->Result->Success === false)
        {
            return $objEntityModelResult;
        }

        $objEntityInsertionResult = $this->insertModelData($objEntityModelResult->Data);

        if ($objEntityInsertionResult->Result->Success === false )
        {
            return $objEntityInsertionResult;
        }

        $this->noFks();

        $newEntityResult = $this->getById($objEntityInsertionResult->Data->new_id);
        $newEntityResult->Result->Query = $objEntityInsertionResult->Result->Query;

        return $newEntityResult;
    }

    public function update(AppModel $objEntityData) : ExcellTransaction
    {
        $objReturnTransaction = new ExcellTransaction();

        if ( empty($objEntityData->{$this->strMainModelPrimary}) )
        {

            $objReturnTransaction->Result->Success = false;
            $objReturnTransaction->Result->Count = 0;
            $objReturnTransaction->Result->Message = "You must supply a valid id for " . $this->strMainModelPrimary . ": " . $objEntityData->{$this->strMainModelPrimary};

            return $objReturnTransaction;
        }

        $objPrimaryKeyValue = $objEntityData->{$this->strMainModelPrimary};


        $objEntityData->ValidateField($this->strMainModelPrimary, $objPrimaryKeyValue);

        if ( empty($objEntityData->{$this->strMainModelPrimary}) || !$objEntityData->ValidateField($this->strMainModelPrimary, $objPrimaryKeyValue))
        {
            $objReturnTransaction->Result->Success = false;
            $objReturnTransaction->Result->Count = 0;
            $objReturnTransaction->Result->Message = "You must supply a valid id for " . $this->strMainModelPrimary . ": " . $objEntityData->{$this->strMainModelPrimary};

            return $objReturnTransaction;
        }

        $objEntityModelResult = $this->buildModel($objEntityData);

        if ( $objEntityModelResult->Result->Success === false)
        {
            return $objEntityModelResult;
        }

        $objEntitUpdateResult = $this->modifyModelData($objEntityModelResult->Data);

        //logText("UpdateInsert.Process.log",json_encode($objEntitUpdateResult));

        if ($objEntitUpdateResult->Result->Success === false )
        {
            return $objEntitUpdateResult;
        }

        $objNewEntityData = $this->getById($objEntitUpdateResult->Data->current_id);
        $objNewEntityData->Result->Query = $objEntitUpdateResult->Result->Query;

        $this->noFks();

        return $objNewEntityData;
    }

    public function deleteById($intEntityId) : ExcellTransaction
    {
        $objDeletionResult = new ExcellTransaction();

        if (!isInteger($intEntityId))
        {
            $objDeletionResult->Result->Success = false;
            $objDeletionResult->Result->Count = 0;
            $objDeletionResult->Result->Message = "The " . $this->strEntityName . " id passed into this deletion method must be an integer.";
            $objDeletionResult->Result->Trace = trace();
            return $objDeletionResult;
        }

        $strQueryForEntityDeletion = "DELETE FROM " . $this->strDatabaseTable . " WHERE " . $this->strMainModelPrimary . " = " . $intEntityId . " LIMIT 1;";

        $this->init();
        return $this->Db->update($strQueryForEntityDeletion);
    }

    public function deleteWhere() : ExcellTransaction
    {
        $objDeletionResult = call_user_func_array("self::getWhere", func_get_args());

        if ($objDeletionResult->Result->Success === false)
        {
            return $objDeletionResult;
        }

        $intDeletionCount = 0;

        foreach($objDeletionResult->Data as $currEntityId => $currEntityData)
        {
            $this->deleteById($currEntityData->{$this->strMainModelPrimary});
            $intDeletionCount++;
        }

        $objDeletionCompletionResult = new ExcellTransaction();

        $objDeletionCompletionResult->Result->Success = true;
        $objDeletionCompletionResult->Result->Count = $intDeletionCount;
        $objDeletionCompletionResult->Result->Message = $intDeletionCount . " rows were successfully deleted.";

        return $objDeletionCompletionResult;
    }

    protected function insertModelData(AppModel $objModel) : ExcellTransaction
    {
        if (empty($objModel->{$this->strMainModelPrimary}))
        {
            $intNextEntityId = $this->getNextId();
            $objModel->{$this->strMainModelPrimary} = $intNextEntityId;
        }
        else
        {
            $intNextEntityId = $objModel->{$this->strMainModelPrimary};
        }

        if ($objModel->HasField("created_on"))
        {
            $objModel->created_on = date("Y-m-d\TH:i:s");
        }

        if ($objModel->HasField("last_updated"))
        {
            $objModel->last_updated = date("Y-m-d\TH:i:s");
        }

        if ($objModel->HasField("sys_row_id"))
        {
            $objModel->sys_row_id = getGuid();
        }

        $strInsertionStringsResult = $this->assembleInsertionStrings($objModel);

        if ($strInsertionStringsResult->Result->Success === false)
        {
            return $strInsertionStringsResult;
        }

        $strInsertionFields = "( " . $strInsertionStringsResult->Data->GetByIndex(0) . " )";
        $strInsertionValues = "( " . $strInsertionStringsResult->Data->GetByIndex(1) . " )";

        $strInsertEntityQuery = "INSERT INTO `" . $this->strDatabaseTable . "` " . $strInsertionFields . " VALUES " . $strInsertionValues . ";";

        /* @var $this->Db Core */

        $objInsertTransaction = $this->Db->update($strInsertEntityQuery);

        if ( $objInsertTransaction->Result->Success === true)
        {
            $objInsertTransaction->Data->new_id = $intNextEntityId;
        }

        return $objInsertTransaction;
    }

    protected function modifyModelData(AppModel $objModel) : ExcellTransaction
    {
        if ($objModel->HasField("last_updated"))
        {
            $objModel->last_updated = date("Y-m-d\TH:i:s");
        }

        $strInsertionStringsResult = $this->assembleUpdateString($objModel);

        $intUpdateModelPrimary = $objModel->{$this->strMainModelPrimary};
        $strPrimaryKeyType = $objModel->getFieldType($this->strMainModelPrimary);

        if ($strInsertionStringsResult->Result->Success === false)
        {
            return $strInsertionStringsResult;
        }

        $intUpdateModelPrimaryWhereId = $intUpdateModelPrimary;

        if ($strPrimaryKeyType !== "int")
        {
            $intUpdateModelPrimaryWhereId = "'{$intUpdateModelPrimary}'";
        }

        $strUpdateFields = $strInsertionStringsResult->Data->GetByIndex(0);

        $strInsertEntityQuery = "UPDATE `" . $this->strDatabaseTable . "` SET " . $strUpdateFields . " WHERE `" . $this->strMainModelPrimary . "` = " . $intUpdateModelPrimaryWhereId . " LIMIT 1;";

        $this->init();
        $objInsertTransaction = $this->Db->update($strInsertEntityQuery);
        $objInsertTransaction->Result->Query = $strInsertEntityQuery;

        if ( $objInsertTransaction->Result->Success === true)
        {
            $objInsertTransaction->Data->current_id = $intUpdateModelPrimary;
        }

        return $objInsertTransaction;
    }

    protected function processWhereClauseRequest($objWhereClauseRequest) : ExcellTransaction
    {
        if ($objWhereClauseRequest === null)
        {
            $objValidationResult = new ExcellTransaction();

            $objValidationResult->Result->Success = true;
            $objValidationResult->Result->Count = 1;
            $objValidationResult->Result->Message = "The whereclause was parsed successfully.";
            $objValidationResult->Data = "";

            return $objValidationResult;
        }

        if (!is_array($objWhereClauseRequest))
        {
            $objFailureTransaction = new ExcellTransaction();

            $objFailureTransaction->Result->Success = false;
            $objFailureTransaction->Result->Count = 0;
            $objFailureTransaction->Result->Message = "The where clause submitted to this process must be an array.";
            $objFailureTransaction->Result->Trace  = trace();

            return $objFailureTransaction;
        }

        $objWhereClause = "(";

        foreach($objWhereClauseRequest as $currWhereClause)
        {
            $objValidationResult = $this->processWhereClauseConditions($currWhereClause);

            if ($objValidationResult->Result->Success === false)
            {
                continue;
            }

            $objWhereClause .= $objValidationResult->Data;

            $this->trimOffErrantlyAppendedAnds($objWhereClause);

            if ( $objValidationResult->Result->Depth > 0 )
            {
                for($intDepthIndex = 0; $intDepthIndex <= $objValidationResult->Result->Depth; $intDepthIndex++)
                {
                    $objWhereClause .= ")";
                }
            }
        }

        $objWhereClause .= ")";

        $objValidationResult = new ExcellTransaction();

        $objValidationResult->Result->Success = true;
        $objValidationResult->Result->Count = 1;
        $objValidationResult->Result->Message = "The where clause was parsed successfully.";
        $objValidationResult->Data = $objWhereClause;

        return $objValidationResult;
    }

    protected function trimOffErrantlyAppendedAnds(&$objWhereClause) : void
    {
        if (substr(trim($objWhereClause), -6) === "AND  )")
        {
            $objWhereClause = str_replace("AND  )",")", $objWhereClause);
        }
    }

    public function processWhereClauseConditions($currWhereClause, $intDepthIndex = 0) : ExcellTransaction
    {
        $objWhereClause = "";
        $lstOperands = array("=","!=",">","<",">=","<=","LIKE","CONTAINS","IN","NOT IN","IS","IS NOT");

        $objValidationResult = new ExcellTransaction();
        $objValidationResult->Result->Depth = $intDepthIndex;
        $objValidationResult->Result->Success = true;
        $objValidationResult->Result->Count = 1;

        if (is_array($currWhereClause) && count($currWhereClause) === 3 && !empty($currWhereClause[0]) && is_string($currWhereClause[0]) && !empty($currWhereClause[1]) && is_string($currWhereClause[1]) && in_array($currWhereClause[1], $lstOperands, true))
        {
            if (strtolower($currWhereClause[1]) !== "in" && strtolower($currWhereClause[1]) !== "not in")
            {
                if ( is_numeric($currWhereClause[2]) === true)
                {
                    $objValidationResult->Data = "{$currWhereClause[0]} {$currWhereClause[1]} {$currWhereClause[2]}";
                }
                elseif ($currWhereClause[2] === "__#ExcellNullable#__")
                {
                    $objValidationResult->Data = "{$currWhereClause[0]} {$currWhereClause[1]} NULL";
                }
                elseif ($currWhereClause[2] === ExcellFalse)
                {
                    $arWhereClauseCombo[] = "{$currWhereClause[0]} {$currWhereClause[1]} false";
                }
                elseif ($currWhereClause[2] === ExcellTrue)
                {
                    $arWhereClauseCombo[] = "{$currWhereClause[0]} {$currWhereClause[1]} true";
                }
                else
                {
                    $objValidationResult->Data = "{$currWhereClause[0]} {$currWhereClause[1]} '{$currWhereClause[2]}'";
                }
            }
            else
            {
                if (!is_array($currWhereClause[2]))
                {
                    $objValidationFailureResult = new ExcellTransaction();
                    $objValidationFailureResult->Result->Depth = $intDepthIndex;
                    $objValidationFailureResult->Result->Success = false;
                    $objValidationFailureResult->Result->Count = 0;

                    return $objValidationFailureResult;
                }

                foreach($currWhereClause[2] as $currWhereInIndex => $currWhereInValue)
                {
                    if ( is_numeric($currWhereClause[2]) === false)
                    {
                        $currWhereClause[2][$currWhereInIndex] = "'" . $currWhereInValue . "'";
                    }
                }

                $strInClause = implode(",",$currWhereClause[2]);

                switch(strtolower($currWhereClause[1]))
                {
                    case "not in":
                        if (is_array($currWhereClause[2]) && count($currWhereClause[2]) > 0)
                        {
                            $objValidationResult->Data = "{$currWhereClause[0]} NOT IN ({$strInClause})";
                        }

                        break;
                    default:
                        $objValidationResult->Data = "{$currWhereClause[0]} IN ({$strInClause})";
                        break;
                }
            }

            return $objValidationResult;
        }
        elseif ( ( !empty($currWhereClause[0]) && is_array($currWhereClause[0]) && count($currWhereClause[0]) === 1 && is_string($currWhereClause[0]) && ( strtolower($currWhereClause[0]) == "or" || strtolower($currWhereClause[0]) == "and" || strtolower($currWhereClause[0]) == "||" || strtolower($currWhereClause[0]) == "&&") ) )
        {
            $objValidationResult->Data = $currWhereClause[0];

            return $objValidationResult;
        }
        elseif ( ( is_string($currWhereClause) && ( strtolower($currWhereClause) == "or" || strtolower($currWhereClause) == "and" || strtolower($currWhereClause) == "||" || strtolower($currWhereClause) == "&&" ) ) )
        {
            $objValidationResult->Data = $currWhereClause;

            return $objValidationResult;
        }
        elseif ( ( is_string($currWhereClause) && strtolower($currWhereClause) == "(" ) )
        {
            $objValidationResult->Data = $currWhereClause[0];

            return $objValidationResult;
        }
        elseif ( ( is_string($currWhereClause) && strtolower($currWhereClause) == ")" ) )
        {
            $objValidationResult->Data = $currWhereClause[0];

            return $objValidationResult;
        }
        elseif ( ( !empty($currWhereClause[0]) && is_array($currWhereClause[0]) && count($currWhereClause[0]) === 1 && is_string($currWhereClause[0]) && strtolower($currWhereClause[0]) == "(" ) )
        {
            $objValidationResult->Data = $currWhereClause[0];

            return $objValidationResult;
        }
        else
        {
            if (!empty($currWhereClause) && is_array($currWhereClause))
            {
                if (empty($currWhereClause[0]))
                {
                    reset($currWhereClause);

                    $arWhereClauseCombo = [];

                    foreach ($currWhereClause as $strWhereClauseField  => $strWhereClauseValue)
                    {
                        if ( is_numeric($currWhereClause) === true)
                        {
                            $arWhereClauseCombo[] = "$strWhereClauseField = {$strWhereClauseValue}";
                        }
                        else
                        {
                            if ($strWhereClauseValue === "__#ExcellNullable#__")
                            {
                                $arWhereClauseCombo[] = "$strWhereClauseField IS NULL";
                            }
                            elseif ($strWhereClauseValue === ExcellFalse)
                            {
                                $arWhereClauseCombo[] = "$strWhereClauseField = false";
                            }
                            elseif ($strWhereClauseValue === ExcellTrue)
                            {
                                $arWhereClauseCombo[] = "$strWhereClauseField = true";
                            }
                            else
                            {
                                $arWhereClauseCombo[] = "$strWhereClauseField = '{$strWhereClauseValue}'";
                            }
                        }
                    }

                    $objValidationResult->Data = "(".implode(" AND ", $arWhereClauseCombo).")";

                    return $objValidationResult;
                }
                else
                {
                    $intNewDepthIndex = $intDepthIndex + 1;

                    $strWhereClauseChildClause = "";
                    $intArrayCounter = 0;

                    foreach ($currWhereClause as $currChildWhereClause)
                    {
                        $intArrayCounter++;
                        $objChildWhereClause = $this->processWhereClauseConditions($currChildWhereClause, $intNewDepthIndex);

                        if (!is_a($objChildWhereClause->Data, App\Utilities\Excell\ExcellCollection::class))
                        {
                            $strWhereClauseChildClause .= $objChildWhereClause->Data . " ";
                        }
                    }

                    if ($intArrayCounter > 1 )
                    {
                        $strWhereClauseChildClause = "(" . $strWhereClauseChildClause . ")";
                    }

                    $objValidationResult->Data =  $strWhereClauseChildClause;

                    return $objValidationResult;
                }
            }
        }

        $objValidationFailureResult = new ExcellTransaction();
        $objValidationFailureResult->Result->Depth = $intDepthIndex;
        $objValidationFailureResult->Result->Success = false;
        $objValidationFailureResult->Result->Count = 0;

        return $objValidationFailureResult;
    }

    public function buildModel($objParamValues) : ExcellTransaction
    {
        /** @var AppModel $objNewModel */
        $objNewModel = new $this->strMainModelName($objParamValues);

        foreach($objNewModel as $currModelKey => $currModelData)
        {
            if (strpos($currModelKey,"__") !== false)
            {
                $strReplacementKey = explode("__", $currModelKey)[0];
                $strReplacementType = explode("__", $currModelKey)[1];

                switch($strReplacementType)
                {
                    case "value":
                        if ($this->blnFksReplace === true)
                        {
                            $objOriginalValue = $objNewModel->{$strReplacementKey};
                            $objNewModel->AddUnvalidatedValue($strReplacementKey, $currModelData);
                            $objNewModel->AddUnvalidatedValue($currModelKey, $objOriginalValue);
                        }
                        break;
                    case "binding":

                        $objNewModel->AddUnvalidatedValue($strReplacementKey, $currModelData);
                        unset($objNewModel->{$currModelKey});
                        break;
                }
            }
        }

        $objTransaction = new ExcellTransaction();
        $objTransaction->Result->Success = true;
        $objTransaction->Result->Message = "success";
        $objTransaction->Result->Count = 1;
        $objTransaction->Data = $objNewModel;

        return $objTransaction;
    }

    public function getController(&$app, $strActiveModule, $strControllerName) : ?AppController
    {
        $strControllerRequest = "Index";

        if (!empty($strControllerName))
        {
            $strControllerRequest = buildControllerClassFromUri($strControllerName);
        }

        $objActiveAppController = null;

        if(!empty($this->lstAppControllers[$strControllerRequest]))
        {
            $objActiveAppController = $this->lstAppControllers[$strControllerRequest];

            if (!empty($objActiveAppController["verb"]))
            {
                if ( $this->app->objHttpRequest->Verb !== $objActiveAppController["verb"] && strtolower($objActiveAppController["verb"]) != "all")
                {
                    $this->app->log($this->strEntityName, "You have approached this controller incorrectly: Error Code: 345203.");
                    return null;
                }
            }
        }

        $strControllerRequest = $strModuleModelPath = "Http\\" . $strActiveModule->Main->Name . "\Controllers\\" . ucwords($strControllerRequest) . "Controller";

        if (!class_exists($strControllerRequest))
        {
            // TODO - Report Error
            //$this->app->log($this->strEntityName, "This controller request does not exist: " . $strControllerRequestPath);
            return null;
        }

        return new $strControllerRequest($app);
    }

    public function validateModel(ExcellHttpModel &$objHttp) : bool
    {
        if(!is_array($this->lstAppModels))
        {
            $objHttp->ValidModelData = false;
            return false;
        }

        foreach($this->lstAppModels as $currModelName => $currModelData)
        {
            // this isn't accurate enough. It needs to identify which type of data works and which does not, as there are post and get params.
            if($currModelData->ValidateModel($objHttp->Data->PostData))
            {
                $objHttp->AuthenticatedModelName = $currModelName;
                $objHttp->AuthenticatedModelType = "PostData";
                $objHttp->ValidModelData = true;
                return true;
            }
            if($currModelData->ValidateModel($objHttp->Params))
            {
                $objHttp->AuthenticatedModelName = $currModelName;
                $objHttp->AuthenticatedModelType = "Params";
                $objHttp->ValidModelData = true;
                return true;
            }
        }

        $objHttp->ValidModelData = false;
        return false;
    }

    public function getFolder()
    {
        return strtolower($this->strEntityName);
    }

    public function renderApp()
    {
        $objAllArgs = func_get_args();

        /** @var VueApp $vueApp */
        list($strViewName, $strThemeId, $vueApp) = func_get_args();

        (new Website($this->app))
            ->InitializePortal($this)
            ->addVueApp($vueApp)
            ->BuildPortalViewContent($strViewName, $objAllArgs)
            ->BuildPageTemplate($strThemeId, true)
            ->RenderPage($strThemeId, true);

        return true;
    }

    public function renderAppPage()
    {
        $objAllArgs = func_get_args();

        list($strViewName, $strThemeId) = func_get_args();

        $currArgsIndex = 0;

        foreach( $objAllArgs as $strArgKey => $strArgObject )
        {
            $currArgsIndex++;

            if ($currArgsIndex == 1 || $currArgsIndex == 2)
            {
                unset($objAllArgs[$strArgKey]);
            }
        }

        $objWebsite = new Website($this->app);
        $objWebsite->InitializePortal($this);
        $objWebsite->BuildPortalViewContent($strViewName, $objAllArgs);

        $objWebsite->BuildPageTemplate($strThemeId, true);

        $objWebsite->RenderPage($strThemeId, true);

        return true;
    }

    public function renderWebsitePage()
    {
        $objAllArgs = func_get_args();

        list($strViewName, $strThemeId) = func_get_args();

        $currArgsIndex = 0;

        foreach( $objAllArgs as $strArgKey => $strArgObject )
        {
            $currArgsIndex++;

            if ($currArgsIndex == 1 || $currArgsIndex == 2)
            {
                unset($objAllArgs[$strArgKey]);
            }
        }

        $objWebsite = new Website($this->app);
        $objWebsite->InitializePortal($this);
        $objWebsite->BuildPortalViewContent($strViewName, $objAllArgs);

        $objWebsite->BuildPageTemplate($strThemeId, false);

        $objWebsite->RenderPage($strThemeId, false);

        return true;
    }

    public function getView()
    {
        $objAllArgs = func_get_args();

        list($strViewName, $strThemeId) = func_get_args();

        $currArgsIndex = 0;

        foreach( $objAllArgs as $strArgKey => $strArgObject )
        {
            $currArgsIndex++;

            if ($currArgsIndex == 1 || $currArgsIndex == 2)
            {
                unset($objAllArgs[$strArgKey]);
            }
        }

        $objWebsite = new Website($this->app);
        $objWebsite->InitializePortal($this);
        $objWebsite->BuildPortalViewContent($strViewName, $objAllArgs);

        return $objWebsite->RenderView();
    }

    public function __set($strField, $objValue)
    {
        $this->{$strField} = $objValue;

        return $this;
    }

    public function __get($strName)
    {
        return $this->{$strName};
    }

    public function __isset($strName)
    {
        return isset($this->{$strName});
    }

    public function debug()
    {
        /* echo '<pre>';
         print_r($this);
         echo '</pre>';*/

        return $this;
    }
}
