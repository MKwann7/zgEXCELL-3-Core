<?php

namespace App\Utilities;

use App;
use App\Utilities\Debug\QueryTracker;
use App\Utilities\Excell\ExcellDatabaseModel;
use App\Utilities\Transaction\ExcellTransaction;
use stdClass;

class Database
{
    protected static $arQueries;
    protected static $Db;
    protected static $app;
    public static $breadcrumb_render = 'default';

    public function __construct ($objDatabaseName = null)
    {
        global $app;

        static::$app = &$app;

        if ($objDatabaseName === null)
        {
            static::$Db = static::$app->objDBs->Main;
        }
        else
        {
            static::$Db = static::$app->objDBs->{$objDatabaseName};
        }
    }

    public static function getQueries() : array
    {
        return static::$arQueries;
    }

    public static function clearQueryCount() : void
    {
        static::$arQueries = [];
    }

    private static function getNewDbConnection(): \mysqli
    {
        if (empty(static::$Db)) {
            static::$Db = static::$app->objDBs->Main;
        }

        return new \mysqli(
            static::$Db->Host,
            static::$Db->Username,
            static::$Db->Password,
            static::$Db->Database
        );
    }

    public static function SetDbConnection($database) : self
    {
        static::$Db = $database;

        return new static;
    }

    public static function ResetDbConnection() : void
    {
        static::$Db = static::$app->objDBs->Main;
    }

    public static function getSimple($strMySqlQuery, $strColumnSort = null) : ExcellTransaction
    {
        mysqli_report(MYSQLI_REPORT_STRICT);

        $objReturnTransaction = new ExcellTransaction();

        if ( empty($strMySqlQuery))
        {
            $objReturnTransaction->result->Success = false;
            $objReturnTransaction->result->Count = 0;
            $objReturnTransaction->result->Message = 'Empty query string passed in';
            $objReturnTransaction->result->Trace = trace();

            return $objReturnTransaction;
        }

        if ( !is_string($strMySqlQuery))
        {
            $objReturnTransaction->result->Success = false;
            $objReturnTransaction->result->Count = 0;
            $objReturnTransaction->result->Message = 'Query passed in must be a string';
            $objReturnTransaction->result->Trace = trace();

            return $objReturnTransaction;
        }

        try
        {
            $objZgXlDb = static::getNewDbConnection();

            if ( $objZgXlDb->connect_errno > 0 )
            {
                $objReturnTransaction->result->Success = false;
                $objReturnTransaction->result->Count = 0;
                $objReturnTransaction->result->Message = 'There was an error connecting to the database [' . $objZgXlDb->error . ']';
                $objReturnTransaction->result->Query = $strMySqlQuery;
                $objReturnTransaction->result->Trace = trace();

                logText("Database.Get.Error.log", 'There was an error connecting to the database [' . $objZgXlDb->error . ']');

                return $objReturnTransaction;
            }

            $objQueryResult = null;
            $tmStart = microtime(true);

            if ( !$objQueryResult = $objZgXlDb->query($strMySqlQuery) )
            {
                $strUpdateError = $objZgXlDb->error;

                //$objZgXlDb->close();

                $objReturnTransaction->result->Success = false;
                $objReturnTransaction->result->Count = 0;
                $objReturnTransaction->result->Message = 'There was an error running the query [' . $strUpdateError . '] '. $strMySqlQuery;
                $objReturnTransaction->result->Query = $strMySqlQuery;
                $objReturnTransaction->result->Trace = trace();

                logText("Database.Update.Error.log", 'There was an error running the query [' . $strUpdateError . '] '. $strMySqlQuery);

                return $objReturnTransaction;
            }

            $tmEnd = microtime(true) - $tmStart;

            static::$arQueries[] = new QueryTracker($strMySqlQuery, $tmEnd, traceArray());

            if ( $objQueryResult->num_rows === 0 )
            {
                //$objZgXlDb->close();

                $objReturnTransaction->result->Success = true;
                $objReturnTransaction->result->Count = 0;
                $objReturnTransaction->result->Message = "No rows found.";
                $objReturnTransaction->result->Query = $strMySqlQuery;
                $objReturnTransaction->result->Trace = trace();

                return $objReturnTransaction;
            }

            $objQueryTransactionResult = new ExcellTransaction();

            if ( !empty($strColumnSort) ) {
                while ( $objQueryResultDataset = $objQueryResult->fetch_object() ) {
                    if (!empty($objQueryResultDataset->$strColumnSort)) {
                        $strResultIndex = $objQueryResultDataset->$strColumnSort;
                        $objQueryTransactionResult->getData()->Add($strResultIndex, $objQueryResultDataset);
                    }
                }
            } else {
                while ( $objQueryResultDataset = $objQueryResult->fetch_object() ) {
                    $objQueryTransactionResult->getData()->Add($objQueryResultDataset);
                }
            }

            //$objZgXlDb->close();

            $intResultCount = $objQueryTransactionResult->getData()->Count();

            $objQueryTransactionResult->result->Success = true;
            $objQueryTransactionResult->result->Count = $intResultCount;
            $objQueryTransactionResult->result->Message = "This query ran successfully";
        }
        catch(\Exception $ex)
        {
            $strUpdateError1 = "unknown";

            if (!empty($objZgXlDb))
            {
                $strUpdateError1 = $objZgXlDb->error;
                //$objZgXlDb->close();
            }

            $strUpdateError2 = $ex->getMessage();

            $objReturnTransaction->result->Success = false;
            $objReturnTransaction->result->Count = 0;
            $objReturnTransaction->result->Message = "An error has occurred [". $strUpdateError1 . "]: " . $strUpdateError2;
            $objReturnTransaction->result->Query = $strMySqlQuery;
            $objReturnTransaction->result->Trace = trace();

            logText("Database.Get.Error.log", "An error has occurred [". $strUpdateError1 . "]: " . $strUpdateError2 . " " . $strMySqlQuery);

            return $objReturnTransaction;
        }

        return $objQueryTransactionResult;
    }

    public static function getComplex($strMySqlQuery, $strJsonColumn = 'data', $strJsonOutput = 'jsondata', $strColumnSort = '') : ExcellTransaction
    {
        $objReturnTransaction = new ExcellTransaction();

        if ( ( is_array($strJsonColumn) && !is_array($strJsonOutput) ) || ( !is_array($strJsonColumn) && is_array($strJsonOutput) ) )
        {
            $objReturnTransaction->result->Success = false;
            $objReturnTransaction->result->Count = 0;
            $objReturnTransaction->result->Message = 'JSON Column or Output should be either both arrays or both strings';
            $objReturnTransaction->result->Trace = trace();

            return $objReturnTransaction;
        }

        if ( ( is_array($strJsonColumn) && is_array($strJsonOutput) ) && count($strJsonColumn) != count($strJsonOutput)  )
        {
            $objReturnTransaction->result->Success = false;
            $objReturnTransaction->result->Count = 0;
            $objReturnTransaction->result->Message = 'JSON Column or Output need to be equal length arrays or both strings';
            $objReturnTransaction->result->Trace = trace();

            return $objReturnTransaction;
        }

        $objQueryResultArray = self::getSimple($strMySqlQuery, $strColumnSort);

        if ( $objQueryResultArray->result->Success != true || $objQueryResultArray->result->Count == 0 )
        {
            return $objQueryResultArray;
        }

        foreach ($objQueryResultArray->data as $key => $da )
        {
            if ( !empty($strJsonOutput) && !is_array($strJsonOutput) )
            {
                $objJsonDecodedObject = json_decode($da->$strJsonColumn, true);

                $objJsonTransaction = new ExcellTransaction();
                $objJsonTransaction->data = $objJsonDecodedObject;

                $objUnBase64Data = self::unBase64Encode($objJsonTransaction);

                if ($objUnBase64Data->result->Success === true)
                {
                    $objQueryResultArray->getData()->$key->$strJsonOutput = $objUnBase64Data->getData();
                }
                else
                {
                    $objQueryResultArray->getData()->$key->$strJsonOutput = null;
                }

                if ($strJsonColumn != $strJsonOutput)
                {
                    unset($objQueryResultArray->getData()->$key->$strJsonColumn);
                }
            }
            elseif (!empty($strJsonOutput))
            {
                $intJsonColumnCount = count($strJsonOutput);

                for ( $currJsonColumnIndex = 0; $currJsonColumnIndex < $intJsonColumnCount; $currJsonColumnIndex++ )
                {
                    $objJsonDecodedObject = json_decode($da[$strJsonColumn[$currJsonColumnIndex]], true);

                    $objJsonTransaction = new ExcellTransaction();
                    $objJsonTransaction->data = $objJsonDecodedObject;

                    $objUnBase64Data = self::unBase64Encode($objJsonTransaction);

                    if ($objUnBase64Data->result->Success === true)
                    {
                        $objQueryResultArray->getData()->$key->$strJsonOutput = $objUnBase64Data->getData();
                    }
                    else
                    {
                        $objQueryResultArray->getData()->$key->$strJsonOutput = null;
                    }

                    if ($strJsonColumn != $strJsonOutput)
                    {
                        unset($objQueryResultArray->getData()->$key->$strJsonColumn[$currJsonColumnIndex]);
                    }
                }
            }
        }

        return $objQueryResultArray;
    }

    public static function update($strMySqlQuery) : ExcellTransaction
    {
        $objReturnTransaction = new ExcellTransaction();

        if (empty($strMySqlQuery))
        {
            $objReturnTransaction->result->Success = false;
            $objReturnTransaction->result->Count = 0;
            $objReturnTransaction->result->Message = 'Empty query string passed in';
            $objReturnTransaction->result->Trace = trace();

            return $objReturnTransaction;
        }

        mysqli_report(MYSQLI_REPORT_STRICT);

        try
        {
            $objZgXlDb = static::getNewDbConnection();

            if ( $objZgXlDb->connect_errno > 0 )
            {
                $strUpdateError = $objZgXlDb->error;

                //$objZgXlDb->close();

                $objReturnTransaction->result->Success = false;
                $objReturnTransaction->result->Count = 0;
                $objReturnTransaction->result->Message = 'There was an error connecting to the database [' . $strUpdateError . ']';
                $objReturnTransaction->result->Trace = trace();

                logText("Database.Update.Error.log", 'There was an error connecting to the database [' . $strUpdateError . ']');

                return $objReturnTransaction;
            }

            $objQueryResult = null;
            $tmStart = microtime(true);

            if ( ! $objQueryResult = $objZgXlDb->query($strMySqlQuery) )
            {
                $strUpdateError = $objZgXlDb->error;

                //$objZgXlDb->close();

                $objReturnTransaction->result->Success = false;
                $objReturnTransaction->result->Count = 0;
                $objReturnTransaction->result->Message = 'There was an error updating the database [' . $strUpdateError . ']';
                $objReturnTransaction->result->Query = $strMySqlQuery;
                $objReturnTransaction->result->Trace = trace();

                logText("Database.Update.Error.log", 'There was an error updating the database [' . $strUpdateError . ']: ' . $strMySqlQuery);

                return $objReturnTransaction;
            }

            $tmEnd = microtime(true) - $tmStart;

            static::$arQueries[] = new QueryTracker($strMySqlQuery, $tmEnd, traceArray());

            //$objZgXlDb->close();

            $objReturnTransaction->result->Success = true;
            $objReturnTransaction->result->Count = 1;
            $objReturnTransaction->result->Message = "This query ran successfully";
            $objReturnTransaction->result->Query = $strMySqlQuery;
            $objReturnTransaction->result->Trace = trace();

            return $objReturnTransaction;
        }
        catch(\Exception $ex)
        {
            $strUpdateError1 = "unknown";

            if (!empty($objZgXlDb))
            {
                $strUpdateError1 = $objZgXlDb->error;
                //$objZgXlDb->close();
            }

            $strUpdateError2 = $ex->getMessage();

            $objReturnTransaction->result->Success = false;
            $objReturnTransaction->result->Count = 0;
            $objReturnTransaction->result->Message = "An error has occurred [". $strUpdateError1 . "]: " . $strUpdateError2;
            $objReturnTransaction->result->Query = $strMySqlQuery;
            $objReturnTransaction->result->Trace = trace();

            logText("Database.Update.Error.log", "An error has occurred [". $strUpdateError1 . "]: " . $strUpdateError2 . " " . $strMySqlQuery);

            return $objReturnTransaction;
        }
    }

    public static function getNextEntityId($strDatabaseTable, $strEntityPrimary) : int
    {
        $query = "SELECT `" . $strEntityPrimary . "` FROM `" . $strDatabaseTable . "` ORDER BY `" . $strEntityPrimary . "` DESC LIMIT 1;";
        $objNextId = static::getSimple($query);

        if ( $objNextId->result->Count === 0)
        {
            return 1000;
        }

        return ($objNextId->getData()->first()->{$strEntityPrimary} + 1);
    }

    public static function unBase64Encode(ExcellTransaction $ay, string $key) : ExcellTransaction
    {
        $objReturnTransaction = new ExcellTransaction();

        $temp_array =  new stdClass();

        if ( is_array($ay->getExtraData($key)) || $ay->getExtraData($key) instanceof \stdClass )
        {
            foreach ($ay->getExtraData($key) as $ky => $dy )
            {
                if ( is_array($dy) || $dy instanceof \stdClass )
                {
                    $objArrayTransaction = new ExcellTransaction();
                    $objArrayTransaction->setExtraData($key, $dy);

                    $temp_array->{$ky} = self::unBase64Encode($objArrayTransaction, $key)->getExtraData($key);
                }
                else
                {
                    if ($dy !== null) {
                        $temp_array->{$ky} = base64_decode($dy);
                    } else {
                        $temp_array->{$ky} = null;
                    }

                }
            }

            $objReturnTransaction->setExtraData($key, $temp_array);
        }
        else
        {
            $objReturnTransaction->result->Success = false;
            $objReturnTransaction->result->Message = 'This Datafield was not an array.';
            $objReturnTransaction->result->Count = 0;
            $objReturnTransaction->clearExtraData();
        }

        return $objReturnTransaction;
    }

    public static function base64Encode(ExcellTransaction $ay, $intDepth = 0) : ExcellTransaction
    {
        $objReturnTransaction = new ExcellTransaction();

        $temp_array =  array();

        if ( is_array($ay->data) )
        {
            foreach ($ay->data as $ky => $dy )
            {
                if ( is_array($dy) )
                {
                    $objArrayTransaction = new ExcellTransaction();
                    $objArrayTransaction->data = $dy;

                    $temp_array[$ky] = self::base64Encode($objArrayTransaction, $intDepth + 1)->getData();
                }
                else
                {
                    $temp_array[$ky] = base64_encode($dy);
                }
            }

            $objReturnTransaction->data = $temp_array;
        }
        else
        {
            $objReturnTransaction->result->Success = false;
            $objReturnTransaction->result->Message = 'This Datafield was not an array.';
            $objReturnTransaction->result->Count = 0;
            $objReturnTransaction->data = null;
        }

        return $objReturnTransaction;
    }

    public static function forceUtf8($str, $inputEnc='WINDOWS-1252' )
    {
        if (!is_a($str, "string"))
        {
            return $str;
        }

        $objChunkedString = str_split($str, 1);
        $strNewString = "";

        if (!empty($objChunkedString ) && is_array($objChunkedString ) && count($objChunkedString) > 0)
        {
            foreach($objChunkedString as $strIndividualCharacter)
            {
                $intAsciiNumber = ord($strIndividualCharacter);
                // Remove non-ascii & non html characters
                if ( ( $intAsciiNumber >= 32 && $intAsciiNumber <= 123 ) || $intAsciiNumber == 160 )
                {
                    if ( $intAsciiNumber == 160 )
                    {
                        $strNewString .= chr(32);
                    }
                    else
                    {
                        $strNewString .= $strIndividualCharacter;
                    }
                }
            }
        }

        return $strNewString;
    }
}
