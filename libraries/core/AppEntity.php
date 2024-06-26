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
    public string $strEntityName;
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
        if ($this->app->getCustomPlatform() === null)
        {
            return 0;
        }

        return $this->app->getCustomPlatform()->getCompanyId();
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

            $this->lstAppTransaction->result->Success = false;
            $this->lstAppTransaction->result->Count = 0;
            $this->lstAppTransaction->result->Message = "This module isn't setup correctly. Error #6934582";

            return $this->lstAppTransaction;
        }

        if (!isset($intEntityRowId) || $intEntityRowId === null)
        {
            $this->lstAppTransaction = new ExcellTransaction();

            $this->lstAppTransaction->result->Success = false;
            $this->lstAppTransaction->result->Count = 0;
            $this->lstAppTransaction->result->Message = "You must pass in an id to retrieve a " . $this->strMainModelName . " row.";

            return $this->lstAppTransaction;
        }

        /** @var AppModel $objEntityModel */
        $objEntityModel = new $this->strMainModelName();

        if(!$objEntityModel->Add($this->strMainModelPrimary, $intEntityRowId))
        {
            $this->lstAppTransaction = new ExcellTransaction();

            $this->lstAppTransaction->result->Success = false;
            $this->lstAppTransaction->result->Count = 0;
            $this->lstAppTransaction->result->Message = "The value passed in for " . $this->strMainModelPrimary ." did not pass validation: " . $intEntityRowId;
            $this->lstAppTransaction->result->Errors = $objEntityModel->Errors;

            return $this->lstAppTransaction;
        }

        return $this->getWhere([$this->strMainModelPrimary => $intEntityRowId], 1);
    }

    public function getBySysRowId($sysRowId) : ExcellTransaction
    {
        if (empty($this->strEntityName) || empty($this->strMainModelName))
        {
            $this->lstAppTransaction = new ExcellTransaction();

            $this->lstAppTransaction->result->Success = false;
            $this->lstAppTransaction->result->Count = 0;
            $this->lstAppTransaction->result->Message = "This module isn't setup correctly. Error #6934582";

            return $this->lstAppTransaction;
        }

        if (!isset($sysRowId) || $sysRowId === null)
        {
            $this->lstAppTransaction = new ExcellTransaction();

            $this->lstAppTransaction->result->Success = false;
            $this->lstAppTransaction->result->Count = 0;
            $this->lstAppTransaction->result->Message = "You must pass in an id to retrieve a " . $this->strMainModelName . " row.";

            return $this->lstAppTransaction;
        }

        /** @var AppModel $objEntityModel */
        $objEntityModel = new $this->strMainModelName();

        if(!$objEntityModel->Add("sys_row_id", $sysRowId))
        {
            $this->lstAppTransaction = new ExcellTransaction();

            $this->lstAppTransaction->result->Success = false;
            $this->lstAppTransaction->result->Count = 0;
            $this->lstAppTransaction->result->Message = "The value passed in for the SysRowId did not pass validation: " . $sysRowId;
            $this->lstAppTransaction->result->Errors = $objEntityModel->Errors;

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
            $objTransaction->result->Success = false;
            $objTransaction->result->Count = 0;
            $objTransaction->result->Message = "This model is not setup correctly.";

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

        if ( $objEntityResult->result->Success === false || $objEntityResult->result->Count === 0 )
        {
            return $objEntityResult;
        }

        foreach($objEntityResult->data as $currEntityIndex => $currEntityResult)
        {
            $objValidatedModel = $this->buildModel($currEntityResult);

            $objEntityResult->getData()->{$currEntityIndex} = $objValidatedModel->getData();
        }

        $objTransaction->result->Success = true;
        $objTransaction->result->Count = $objEntityResult->result->Count;
        $objTransaction->result->Message = "This query returned " . $objEntityResult->result->Count . " Results.";
        $objTransaction->data = $objEntityResult->getData();

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
            $objTransaction->result->Success = false;
            $objTransaction->result->Count = 0;
            $objTransaction->result->Message = "This model is not setup correctly.";

            return $objTransaction;
        }

        $objAllArgs = func_get_args();

        $objWhereClauseRequest = self::assembleWhereClauseArray($objAllArgs);

        if ( $objWhereClauseRequest->result->Success === false)
        {
            return $objWhereClauseRequest;
        }

        $objWhereClause = $this->processWhereClauseRequest($objWhereClauseRequest->data["WhereClause"]);

        $objWhereSortByFilter = $objWhereClauseRequest->data["SortBy"];
        $objWhereClauseLimit = $objWhereClauseRequest->data["Limit"];

        if ($objWhereClause->result->Success === false)
        {
            return $objWhereClause;
        }

        $strWhereClause = !empty($objWhereClause->getData()->first()) ? " WHERE " . $objWhereClause->getData()->first() : "";

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

        if ( $objEntityResult->result->Success === false || $objEntityResult->result->Count === 0 )
        {
            return $objEntityResult;
        }

        if ($this->selecteCountTrue === true)
        {
            $count = new \stdClass();
            $count->count = $objEntityResult->getData()->first()->{"COUNT(*)"};
            $objTransaction->result->Success = true;
            $objTransaction->result->Count = $count->count;
            $objTransaction->result->Message = "This query returned " . $count->count . " Results.";
            $objTransaction->result->Query = $strGetEntityByWhereClauseQuery;
            $objTransaction->data = $count;

            $this->noFks();
            $this->selecteCountTrue = false;

            return $objTransaction;
        }

        foreach($objEntityResult->getData() as $currEntityIndex => $currEntityResult)
        {
            $colValidatedModel = $this->buildModel($currEntityResult)->getData()->first();
            $colValidatedModel->ConvertDataTypes();

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

                        $colJoinedValidatedModel = $objModule->buildModel($currEntityResult)->getData();

                        $colValidatedModel->AddUnvalidatedValue($objModule->strMainModelName, $colJoinedValidatedModel);
                    }
                }
            }

            $collection = $objEntityResult->getData();
            $collection->{$currEntityIndex} = $colValidatedModel;
            $objEntityResult->setData($collection);
        }

        $objTransaction->result->Success = true;
        $objTransaction->result->Count = $objEntityResult->result->Count;
        $objTransaction->result->Message = "This query returned " . $objEntityResult->result->Count . " Results.";
        $objTransaction->result->Query = $strGetEntityByWhereClauseQuery;
        $objTransaction->data = $objEntityResult->getData();

        $this->noFks();

        return $objTransaction;
    }

    public function getWhereFkv() : ExcellTransaction
    {
        $objTransaction = new ExcellTransaction();

        if ( empty($this->strEntityName) || empty($this->strMainModelName) )
        {
            $objTransaction->result->Success = false;
            $objTransaction->result->Count = 0;
            $objTransaction->result->Message = "This model is not setup correctly.";

            return $objTransaction;
        }

        $objAllArgs = func_get_args();

        if(empty($objAllArgs[0]) || !is_array($objAllArgs[0]))
        {
            $objTransaction->result->Success = false;
            $objTransaction->result->Count = 0;
            $objTransaction->result->Message = "No request array was provided to parse.";

            return $objTransaction;
        }

        $objWhereClauseRequest = self::assembleWhereClauseArray($objAllArgs);

        if ( $objWhereClauseRequest->result->Success === false)
        {
            return $objWhereClauseRequest;
        }

        $objWhereSortByFilter = $objWhereClauseRequest->data["SortBy"];
        $objWhereClauseLimit = $objWhereClauseRequest->data["Limit"];

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

        if ( $objEntityResult->result->Success === false || $objEntityResult->result->Count === 0 )
        {
            return $objEntityResult;
        }

        foreach($objEntityResult->data as $currEntityIndex => $currEntityResult)
        {
            $objValidatedModel = $this->buildModel($currEntityResult);

            $objValidatedModel->getData()->ConvertDataTypes();

            $objEntityResult->getData()->{$currEntityIndex} = $objValidatedModel->getData();
        }

        $objTransaction->result->Success = true;
        $objTransaction->result->Count = $objEntityResult->result->Count;
        $objTransaction->result->Message = "This query returned " . $objEntityResult->result->Count . " Results.";
        $objTransaction->result->Query = $strGetEntityByWhereClauseQuery;
        $objTransaction->data = $objEntityResult->getData();

        $this->noFks();

        return $objTransaction;
    }

    public function getWhereIn($field, $values, $sortBy = null, $limit = null) : ExcellTransaction
    {
        $objTransaction = new ExcellTransaction();

        if (!empty($values) && !is_array($values))
        {
            $objTransaction->result->Success = false;
            $objTransaction->result->Count = 0;
            $objTransaction->result->Message = "You must include an array as a second parameter for the getWhereIn method to work.";

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
                $objReturnTransaction->result->Success = false;
                $objReturnTransaction->result->Count = 0;
                $objReturnTransaction->result->Message = "This where clause request was not is not setup correctly: " . json_encode($objAllArgs);
            }
        }

        $objReturnTransaction->result->Success = true;
        $objReturnTransaction->result->Count = 1;
        $objReturnTransaction->data = array("WhereClause" => $objWhereClauseRequest, "SortBy" => $objWhereSortByFilter, "Limit" => $objWhereClauseLimit);

        return $objReturnTransaction;
    }

    protected function assembleUpdateString($objModel) : ExcellTransaction
    {
        $objReturnTransaction = new ExcellTransaction();

        $arUpdateFields = array();

        $objModel->processFieldValuesForDatabaseUpdate();

        foreach($objModel as $currFieldName => $currFieldValue)
        {
            if ((!empty($currFieldValue) || $currFieldValue === 0) && $currFieldName !== "created_on" && $currFieldName !== "sys_row_id" && $currFieldName != $this->strMainModelPrimary && strpos($currFieldName, "__value") === false)
            {
                $strUpdateFieldValue = "`" . $currFieldName . "` = ";

                if ( $currFieldValue === EXCELL_NULL)
                {
                    $strUpdateFieldValue .= 'null';
                }
                elseif ( $currFieldValue === EXCELL_TRUE)
                {
                    $strUpdateFieldValue .= 'TRUE';
                }
                elseif ( $currFieldValue === EXCELL_FALSE)
                {
                    $strUpdateFieldValue .= 'FALSE';
                }
                elseif ( $currFieldValue === EXCELL_EMPTY_STRING)
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
                elseif ( is_a($currFieldValue, ExcellCollection::class) || is_a($currFieldValue, \stdClass::class))
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
                        $objValueTransaction->data = $currFieldValue;

                        $currFieldValue = json_encode(Database::base64Encode($objValueTransaction)->data);
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

        $objReturnTransaction->getData()->{0} = implode(", ", $arUpdateFields);

        $objReturnTransaction->result->Success = true;
        $objReturnTransaction->result->Count = 1;

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
            if ( $currFieldValue === EXCELL_NULL) {
                $arInsertionValues[] = 'null';
            } elseif ( $currFieldValue === EXCELL_TRUE) {
                $arInsertionValues[] = 'TRUE';
            } elseif ( $currFieldValue === EXCELL_FALSE) {
                $arInsertionValues[] = 'FALSE';
            } elseif ( $currFieldValue === EXCELL_EMPTY_STRING) {
                $arInsertionValues[] = "''";
            } elseif ( $currFieldName === "last_updated" || $currFieldName === "created_on" ) {
                if (!empty($currFieldValue) && strtotime($currFieldValue) !== false) {
                    $arInsertionValues[] = "'" . date("Y-m-d\TH:i:s", strtotime($currFieldValue)) . "'";
                } else {
                    $arInsertionValues[] = "'" . date("Y-m-d\TH:i:s") . "'";
                }
            } elseif ( isDecimal($currFieldValue) || isInteger($currFieldValue)) {
                $arInsertionValues[] = $currFieldValue;
            } else {
                if ($objModel->getFieldType($currFieldName) === "string" || $objModel->getFieldType($currFieldName) === "varchar") {
                    $currFieldValue = str_replace("'", "\'", str_replace("\'", "'", str_replace('&#39;', "'", $currFieldValue ?? "")));
                }

                if ($objModel->getFieldType($currFieldName) === "json") {
                    if (isJson($currFieldValue) === true) {
                        if (is_object($currFieldValue) && is_a($currFieldValue,"stdClass")) {
                            $currFieldValue = json_decode(json_encode($currFieldValue, JSON_FORCE_OBJECT), true);
                        } elseif (!is_array($currFieldValue)) {
                            $currFieldValue =  json_decode($currFieldValue, true);
                        }
                    }

                    $objValueTransaction = new ExcellTransaction();
                    $objValueTransaction->data = $currFieldValue;

                    $currFieldValue = json_encode(Database::base64Encode($objValueTransaction)->data);
                }

                $arInsertionValues[] = "'" . $currFieldValue . "'";
            }
        }

        $collection = new ExcellCollection();

        $line1 = new \stdClass();
        $line1->string = implode(", ", $arInsertionFields);
        $line2 = new \stdClass();
        $line2->string = implode(", ", $arInsertionValues);

        $collection->Add(0, $line1);
        $collection->Add(1, $line2);

        $objReturnTransaction->setData($collection);
        $objReturnTransaction->result->Success = true;
        $objReturnTransaction->result->Count = 2;

        return $objReturnTransaction;
    }

    protected function getNextId() : int
    {
        $this->init();
        return $this->Db->getNextEntityId($this->strDatabaseTable, $this->strMainModelPrimary);
    }

    public function createNew($objEntityData) : ExcellTransaction
    {
        if (!is_subclass_of($objEntityData, AppModel::class)) {
            $this->lstAppTransaction = new ExcellTransaction();

            $this->lstAppTransaction->result->Success = false;
            $this->lstAppTransaction->result->Count = 0;
            $this->lstAppTransaction->result->Message = "You must pass in a data model.";

            return $this->lstAppTransaction;
        }

        $objEntityModelResult = $this->buildModel($objEntityData);

        if ( $objEntityModelResult->result->Success === false) {
            return $objEntityModelResult;
        }

        $objEntityInsertionResult = $this->insertModelData($objEntityModelResult->data->first());

        if ($objEntityInsertionResult->result->Success === false ) {
            return $objEntityInsertionResult;
        }

        $this->noFks();

        $newEntityResult = $this->getById($objEntityInsertionResult->getExtraData("new_id"));
        $newEntityResult->result->Query = $objEntityInsertionResult->getResult()->Query;

        return $newEntityResult;
    }

    public function update(AppModel $objEntityData) : ExcellTransaction
    {
        $objReturnTransaction = new ExcellTransaction();

        if (empty($objEntityData->{$this->strMainModelPrimary})) {

            $objReturnTransaction->result->Success = false;
            $objReturnTransaction->result->Count = 0;
            $objReturnTransaction->result->Message = "You must supply a valid id for " . $this->strMainModelPrimary . ": " . $objEntityData->{$this->strMainModelPrimary};

            return $objReturnTransaction;
        }

        $objPrimaryKeyValue = $objEntityData->{$this->strMainModelPrimary};


        $objEntityData->ValidateField($this->strMainModelPrimary, $objPrimaryKeyValue);

        if (
            empty($objEntityData->{$this->strMainModelPrimary}) ||
            !$objEntityData->ValidateField($this->strMainModelPrimary, $objPrimaryKeyValue)
        ) {
            $objReturnTransaction->result->Success = false;
            $objReturnTransaction->result->Count = 0;
            $objReturnTransaction->result->Message = "You must supply a valid id for " . $this->strMainModelPrimary . ": " . $objEntityData->{$this->strMainModelPrimary};

            return $objReturnTransaction;
        }

        $objEntityModelResult = $this->buildModel($objEntityData);

        if ( $objEntityModelResult->result->Success === false) {
            return $objEntityModelResult;
        }

        $objEntityUpdateResult = $this->modifyModelData($objEntityModelResult->getData()->first());

        if ($objEntityUpdateResult->result->Success === false ) {
            return $objEntityUpdateResult;
        }

        $objNewEntityData = $this->getById($objEntityUpdateResult->getExtraData("current_id"));
        $objNewEntityData->result->Query = $objEntityUpdateResult->result->Query;

        $this->noFks();

        return $objNewEntityData;
    }

    public function deleteById($intEntityId) : ExcellTransaction
    {
        $objDeletionResult = new ExcellTransaction();

        if (!isInteger($intEntityId)) {
            $objDeletionResult->result->Success = false;
            $objDeletionResult->result->Count = 0;
            $objDeletionResult->result->Message = "The " . $this->strEntityName . " id passed into this deletion method must be an integer.";
            $objDeletionResult->result->Trace = trace();
            return $objDeletionResult;
        }

        $strQueryForEntityDeletion = "DELETE FROM " . $this->strDatabaseTable . " WHERE " . $this->strMainModelPrimary . " = " . $intEntityId . " LIMIT 1;";

        $this->init();
        return $this->Db->update($strQueryForEntityDeletion);
    }

    public function deleteWhere() : ExcellTransaction
    {
        $objDeletionResult = call_user_func_array("self::getWhere", func_get_args());

        if ($objDeletionResult->Result->Success === false) {
            return $objDeletionResult;
        }

        $intDeletionCount = 0;

        foreach($objDeletionResult->Data as $currEntityId => $currEntityData) {
            $this->deleteById($currEntityData->{$this->strMainModelPrimary});
            $intDeletionCount++;
        }

        $objDeletionCompletionResult = new ExcellTransaction();

        $objDeletionCompletionResult->result->Success = true;
        $objDeletionCompletionResult->result->Count = $intDeletionCount;
        $objDeletionCompletionResult->result->Message = $intDeletionCount . " rows were successfully deleted.";

        return $objDeletionCompletionResult;
    }

    protected function insertModelData(AppModel $objModel) : ExcellTransaction
    {
        if (empty($objModel->{$this->strMainModelPrimary})) {
            $intNextEntityId = $this->getNextId();
            $objModel->{$this->strMainModelPrimary} = $intNextEntityId;
        } else {
            $intNextEntityId = $objModel->{$this->strMainModelPrimary};
        }

        if (
            $objModel->HasField("created_on") &&
            (
                empty($objModel->created_on) ||
                strtotime($objModel->created_on) === false
            )
        ) {
            $objModel->created_on = date("Y-m-d\TH:i:s");
        }

        if (
            $objModel->HasField("last_updated") &&
            (
                empty($objModel->last_updated) ||
                strtotime($objModel->last_updated) === false
            )
        ) {
            $objModel->last_updated = date("Y-m-d\TH:i:s");
        }

        if ($objModel->HasField("sys_row_id")) {
            $objModel->sys_row_id = getGuid();
        }

        $strInsertionStringsResult = $this->assembleInsertionStrings($objModel);

        if ($strInsertionStringsResult->result->Success === false) {
            return $strInsertionStringsResult;
        }

        $strInsertionFields = "( " . $strInsertionStringsResult->getData()->GetByIndex(0)->string . " )";
        $strInsertionValues = "( " . $strInsertionStringsResult->getData()->GetByIndex(1)->string . " )";

        $strInsertEntityQuery = "INSERT INTO `" . $this->strDatabaseTable . "` " . $strInsertionFields . " VALUES " . $strInsertionValues . ";";

        /* @var $this->Db Core */

        $objInsertTransaction = $this->Db->update($strInsertEntityQuery);

        if ( $objInsertTransaction->result->Success === true) {
            $objInsertTransaction->setExtraData("new_id", $intNextEntityId);
        }

        return $objInsertTransaction;
    }

    protected function modifyModelData(AppModel $objModel) : ExcellTransaction
    {
        if ($objModel->HasField("last_updated")) {
            $objModel->last_updated = date("Y-m-d\TH:i:s");
        }

        $strInsertionStringsResult = $this->assembleUpdateString($objModel);

        $intUpdateModelPrimary = $objModel->{$this->strMainModelPrimary};
        $strPrimaryKeyType = $objModel->getFieldType($this->strMainModelPrimary);

        if ($strInsertionStringsResult->result->Success === false) {
            return $strInsertionStringsResult;
        }

        $intUpdateModelPrimaryWhereId = $intUpdateModelPrimary;

        if ($strPrimaryKeyType !== "int") {
            $intUpdateModelPrimaryWhereId = "'{$intUpdateModelPrimary}'";
        }

        $strUpdateFields = $strInsertionStringsResult->getData()->GetByIndex(0);

        $strInsertEntityQuery = "UPDATE `" . $this->strDatabaseTable . "` SET " . $strUpdateFields . " WHERE `" . $this->strMainModelPrimary . "` = " . $intUpdateModelPrimaryWhereId . " LIMIT 1;";

        $this->init();
        $objInsertTransaction = $this->Db->update($strInsertEntityQuery);
        $objInsertTransaction->result->Query = $strInsertEntityQuery;

        if ( $objInsertTransaction->result->Success === true) {
            $objInsertTransaction->setExtraData("current_id", $intUpdateModelPrimary);
        }

        return $objInsertTransaction;
    }

    protected function processWhereClauseRequest($objWhereClauseRequest) : ExcellTransaction
    {
        if ($objWhereClauseRequest === null) {
            $objValidationResult = new ExcellTransaction();

            $objValidationResult->result->Success = true;
            $objValidationResult->result->Count = 1;
            $objValidationResult->result->Message = "The whereclause was parsed successfully.";
            $objValidationResult->setData(new ExcellCollection());

            return $objValidationResult;
        }

        if (!is_array($objWhereClauseRequest)) {
            $objFailureTransaction = new ExcellTransaction();

            $objFailureTransaction->result->Success = false;
            $objFailureTransaction->result->Count = 0;
            $objFailureTransaction->result->Message = "The where clause submitted to this process must be an array.";
            $objFailureTransaction->result->Trace  = trace();

            return $objFailureTransaction;
        }

        $objWhereClause = "(";

        foreach($objWhereClauseRequest as $currWhereClause) {
            $objValidationResult = $this->processWhereClauseConditions($currWhereClause);

            if ($objValidationResult->result->Success === false) {
                continue;
            }

            $objWhereClause .= $objValidationResult->getData()->first();

            $this->trimOffErrantlyAppendedAnds($objWhereClause);

            if ( $objValidationResult->result->Depth > 0 ) {
                for ($intDepthIndex = 0; $intDepthIndex <= $objValidationResult->result->Depth; $intDepthIndex++) {
                    $objWhereClause .= ")";
                }
            }
        }

        $objWhereClause .= ")";

        $objValidationResult = new ExcellTransaction();

        $objValidationResult->result->Success = true;
        $objValidationResult->result->Count = 1;
        $objValidationResult->result->Message = "The where clause was parsed successfully.";

        $whereClause = new ExcellCollection();
        $whereClause->Add("whereClause", $objWhereClause);

        $objValidationResult->setData($whereClause);

        return $objValidationResult;
    }

    protected function trimOffErrantlyAppendedAnds(&$objWhereClause) : void
    {
        if (substr(trim($objWhereClause), -6) === "AND  )") {
            $objWhereClause = str_replace("AND  )",")", $objWhereClause);
        }
    }

    public function processWhereClauseConditions($currWhereClause, $intDepthIndex = 0) : ExcellTransaction
    {
        $whereClause = $this->getWhereClauseFromRequest($currWhereClause, $intDepthIndex);

        $objWhereClause = new ExcellCollection();
        $objWhereClause->Add("whereClause", $whereClause);

        $objValidationFailureResult = new ExcellTransaction(
            $whereClause !== "",
            "",
            $objWhereClause,
            $whereClause !== "" ? 1 : 0
        );

        $objValidationFailureResult->result->Depth = $intDepthIndex;

        return $objValidationFailureResult;
    }

    protected function getWhereClauseFromRequest($currWhereClause, $intDepthIndex) : string
    {
        $lstOperands = array("=","!=",">","<",">=","<=","LIKE","CONTAINS","IN","NOT IN","IS","IS NOT");
        $whereClause = "";

        if (
            is_array($currWhereClause) &&
            count($currWhereClause) === 3 &&
            !empty($currWhereClause[0]) &&
            is_string($currWhereClause[0]) &&
            !empty($currWhereClause[1]) &&
            is_string($currWhereClause[1]) &&
            in_array($currWhereClause[1], $lstOperands, true)
        ) {
            if (
                strtolower($currWhereClause[1]) !== "in" &&
                strtolower($currWhereClause[1]) !== "not in"
            ) {
                if ( is_numeric($currWhereClause[2]) === true) {
                    $whereClause = "{$currWhereClause[0]} {$currWhereClause[1]} {$currWhereClause[2]}";
                }  elseif ($currWhereClause[2] === "__#ExcellNullable#__") {
                    $whereClause = "{$currWhereClause[0]} {$currWhereClause[1]} NULL";
                } elseif ($currWhereClause[2] === EXCELL_FALSE) {
                    $arWhereClauseCombo[] = "{$currWhereClause[0]} {$currWhereClause[1]} false";
                } elseif ($currWhereClause[2] === EXCELL_TRUE) {
                    $arWhereClauseCombo[] = "{$currWhereClause[0]} {$currWhereClause[1]} true";
                } else {
                    $whereClause = "{$currWhereClause[0]} {$currWhereClause[1]} '{$currWhereClause[2]}'";
                }
            } else {
                if (!is_array($currWhereClause[2])) {
                    $objValidationFailureResult = new ExcellTransaction();
                    $objValidationFailureResult->result->Depth = $intDepthIndex;
                    $objValidationFailureResult->result->Success = false;
                    $objValidationFailureResult->result->Count = 0;

                    return "";
                }

                foreach($currWhereClause[2] as $currWhereInIndex => $currWhereInValue) {
                    if ( is_numeric($currWhereClause[2]) === false) {
                        $currWhereClause[2][$currWhereInIndex] = "'" . $currWhereInValue . "'";
                    }
                }

                $strInClause = implode(",",$currWhereClause[2]);

                $i = strtolower($currWhereClause[1]);

                if ($i == "not in")  {
                    if (is_array($currWhereClause[2]) && count($currWhereClause[2]) > 0) {
                        $whereClause = "{$currWhereClause[0]} NOT IN ({$strInClause})";
                    }
                } else {
                    $whereClause = "{$currWhereClause[0]} IN ({$strInClause})";
                }
            }

            return $whereClause;
        } elseif (
            !empty($currWhereClause[0]) &&
            is_array($currWhereClause[0]) &&
            count($currWhereClause[0]) === 1 &&
            is_string($currWhereClause[0]) &&
            (
                strtolower($currWhereClause[0]) == "or" ||
                strtolower($currWhereClause[0]) == "and" ||
                strtolower($currWhereClause[0]) == "||" ||
                strtolower($currWhereClause[0]) == "&&"
            )
        ) {
            return $currWhereClause[0];
        } elseif (
            is_string($currWhereClause) &&
            (
                strtolower($currWhereClause) == "or" ||
                strtolower($currWhereClause) == "and" ||
                strtolower($currWhereClause) == "||" ||
                strtolower($currWhereClause) == "&&"
            )
        ) {
            return $currWhereClause;
        } elseif (
            is_string($currWhereClause) &&
            strtolower($currWhereClause) == "("
        ) {
            return $currWhereClause[0];
        } elseif (
            is_string($currWhereClause) &&
            strtolower($currWhereClause) == ")"
        ) {
            return $currWhereClause[0];
        } elseif (
            !empty($currWhereClause[0]) &&
            is_array($currWhereClause[0]) &&
            count($currWhereClause[0]) === 1 &&
            is_string($currWhereClause[0]) &&
            strtolower($currWhereClause[0]) == "("
        ) {
            return $currWhereClause[0];
        } else {
            if (!empty($currWhereClause) && is_array($currWhereClause)) {
                if (empty($currWhereClause[0])) {
                    reset($currWhereClause);

                    $arWhereClauseCombo = [];

                    foreach ($currWhereClause as $strWhereClauseField  => $strWhereClauseValue) {
                        if ( is_numeric($currWhereClause) === true) {
                            $arWhereClauseCombo[] = "$strWhereClauseField = {$strWhereClauseValue}";
                        } else {
                            if ($strWhereClauseValue === "__#ExcellNullable#__") {
                                $arWhereClauseCombo[] = "$strWhereClauseField IS NULL";
                            } elseif ($strWhereClauseValue === EXCELL_FALSE) {
                                $arWhereClauseCombo[] = "$strWhereClauseField = false";
                            } elseif ($strWhereClauseValue === EXCELL_TRUE) {
                                $arWhereClauseCombo[] = "$strWhereClauseField = true";
                            } else {
                                $arWhereClauseCombo[] = "$strWhereClauseField = '{$strWhereClauseValue}'";
                            }
                        }
                    }

                    return "(".implode(" AND ", $arWhereClauseCombo).")";
                } else {
                    $intNewDepthIndex = $intDepthIndex + 1;

                    $strWhereClauseChildClause = "";
                    $intArrayCounter = 0;

                    foreach ($currWhereClause as $currChildWhereClause) {
                        $intArrayCounter++;
                        $objChildWhereClause = $this->processWhereClauseConditions($currChildWhereClause, $intNewDepthIndex);

                        $strWhereClauseChildClause .= $objChildWhereClause->getData()->first() . " ";
                    }

                    if ($intArrayCounter > 1 ) {
                        $strWhereClauseChildClause = "(" . $strWhereClauseChildClause . ")";
                    }

                    return $strWhereClauseChildClause;
                }
            }
        }

        return "";
    }

    public function buildModel($objParamValues) : ExcellTransaction
    {
        /** @var AppModel $objNewModel */
        $objNewModel = new $this->strMainModelName($objParamValues);

        foreach($objNewModel as $currModelKey => $currModelData) {
            if (str_contains($currModelKey, "__")) {
                $strReplacementKey = explode("__", $currModelKey)[0];
                $strReplacementType = explode("__", $currModelKey)[1];

                switch($strReplacementType) {
                    case "value":
                        if ($this->blnFksReplace === true) {
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

        $collection = new ExcellCollection();
        $collection->Add($objNewModel);

        $objTransaction = new ExcellTransaction();
        $objTransaction->result->Success = true;
        $objTransaction->result->Message = "success";
        $objTransaction->result->Count = 1;
        $objTransaction->setData($collection);

        return $objTransaction;
    }

    public function getController(&$app, $strActiveModule, $strControllerName) : ?AppController
    {
        $strControllerRequest = "Index";

        if (!empty($strControllerName)) {
            $strControllerRequest = buildControllerClassFromUri($strControllerName);
        }

        $objActiveAppController = null;

        if (!empty($this->lstAppControllers[$strControllerRequest])) {
            $objActiveAppController = $this->lstAppControllers[$strControllerRequest];

            if (!empty($objActiveAppController["verb"])) {
                if (
                    $this->app->objHttpRequest->Verb !== $objActiveAppController["verb"] &&
                    strtolower($objActiveAppController["verb"]) != "all"
                ) {
                    $this->app->log($this->strEntityName, "You have approached this controller incorrectly: Error Code: 345203.");
                    return null;
                }
            }
        }

        $strControllerRequest = $strModuleModelPath = "Http\\" . $strActiveModule->Main->Name . "\Controllers\\" . ucwords($strControllerRequest) . "Controller";

        if (!class_exists($strControllerRequest)) {
            // TODO - Report Error
            //$this->app->log($this->strEntityName, "This controller request does not exist: " . $strControllerRequestPath);
            return null;
        }

        return new $strControllerRequest($app);
    }

    public function validateModel(ExcellHttpModel &$objHttp) : bool
    {
        if (!is_array($this->lstAppModels)) {
            $objHttp->ValidModelData = false;
            return false;
        }

        foreach($this->lstAppModels as $currModelName => $currModelData) {
            // this isn't accurate enough. It needs to identify which type of data works and which does not, as there are post and get params.
            if ($currModelData->ValidateModel($objHttp->Data->PostData)) {
                $objHttp->AuthenticatedModelName = $currModelName;
                $objHttp->AuthenticatedModelType = "PostData";
                $objHttp->ValidModelData = true;
                return true;
            }

            if ($currModelData->ValidateModel($objHttp->Params)) {
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

        foreach( $objAllArgs as $strArgKey => $strArgObject ) {
            $currArgsIndex++;

            if ($currArgsIndex == 1 || $currArgsIndex == 2) {
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

        foreach( $objAllArgs as $strArgKey => $strArgObject ) {
            $currArgsIndex++;

            if ($currArgsIndex == 1 || $currArgsIndex == 2) {
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

        foreach( $objAllArgs as $strArgKey => $strArgObject ) {
            $currArgsIndex++;

            if ($currArgsIndex == 1 || $currArgsIndex == 2) {
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
