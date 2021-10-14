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
            $objReturnTransaction->Result->Success = false;
            $objReturnTransaction->Result->Count = 0;
            $objReturnTransaction->Result->Message = 'Empty query string passed in';
            $objReturnTransaction->Result->Trace = trace();

            return $objReturnTransaction;
        }

        if ( !is_string($strMySqlQuery))
        {
            $objReturnTransaction->Result->Success = false;
            $objReturnTransaction->Result->Count = 0;
            $objReturnTransaction->Result->Message = 'Query passed in must be a string';
            $objReturnTransaction->Result->Trace = trace();

            return $objReturnTransaction;
        }

        try
        {
            $objZgXlDb = static::getNewDbConnection();

            if ( $objZgXlDb->connect_errno > 0 )
            {
                $objReturnTransaction->Result->Success = false;
                $objReturnTransaction->Result->Count = 0;
                $objReturnTransaction->Result->Message = 'There was an error connecting to the database [' . $objZgXlDb->error . ']';
                $objReturnTransaction->Result->Query = $strMySqlQuery;
                $objReturnTransaction->Result->Trace = trace();

                logText("Database.Get.Error.log", 'There was an error connecting to the database [' . $objZgXlDb->error . ']');

                return $objReturnTransaction;
            }

            $objQueryResult = null;
            $tmStart = microtime(true);

            if ( !$objQueryResult = $objZgXlDb->query($strMySqlQuery) )
            {
                $strUpdateError = $objZgXlDb->error;

                //$objZgXlDb->close();

                $objReturnTransaction->Result->Success = false;
                $objReturnTransaction->Result->Count = 0;
                $objReturnTransaction->Result->Message = 'There was an error running the query [' . $strUpdateError . '] '. $strMySqlQuery;
                $objReturnTransaction->Result->Query = $strMySqlQuery;
                $objReturnTransaction->Result->Trace = trace();

                logText("Database.Update.Error.log", 'There was an error running the query [' . $strUpdateError . '] '. $strMySqlQuery);

                return $objReturnTransaction;
            }

            $tmEnd = microtime(true) - $tmStart;

            static::$arQueries[] = new QueryTracker($strMySqlQuery, $tmEnd, traceArray());

            if ( $objQueryResult->num_rows === 0 )
            {
                //$objZgXlDb->close();

                $objReturnTransaction->Result->Success = true;
                $objReturnTransaction->Result->Count = 0;
                $objReturnTransaction->Result->Message = "No rows found.";
                $objReturnTransaction->Result->Query = $strMySqlQuery;
                $objReturnTransaction->Result->Trace = trace();

                return $objReturnTransaction;
            }

            $objQueryTransactionResult = new ExcellTransaction();

            if ( !empty($strColumnSort) )
            {
                while ( $objQueryResultDataset = $objQueryResult->fetch_object() )
                {
                    $strResultIndex = $objQueryResultDataset->$strColumnSort;
                    $objQueryTransactionResult->Data->Add($strResultIndex, $objQueryResultDataset);
                }
            }
            else
            {
                while ( $objQueryResultDataset = $objQueryResult->fetch_object() )
                {
                    $objQueryTransactionResult->Data->Add($objQueryResultDataset);
                }
            }

            //$objZgXlDb->close();

            $intResultCount = $objQueryTransactionResult->Data->Count();

            $objQueryTransactionResult->Result->Success = true;
            $objQueryTransactionResult->Result->Count = $intResultCount;
            $objQueryTransactionResult->Result->Message = "This query ran successfully";
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

            $objReturnTransaction->Result->Success = false;
            $objReturnTransaction->Result->Count = 0;
            $objReturnTransaction->Result->Message = "An error has occurred [". $strUpdateError1 . "]: " . $strUpdateError2;
            $objReturnTransaction->Result->Query = $strMySqlQuery;
            $objReturnTransaction->Result->Trace = trace();

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
            $objReturnTransaction->Result->Success = false;
            $objReturnTransaction->Result->Count = 0;
            $objReturnTransaction->Result->Message = 'JSON Column or Output should be either both arrays or both strings';
            $objReturnTransaction->Result->Trace = trace();

            return $objReturnTransaction;
        }

        if ( ( is_array($strJsonColumn) && is_array($strJsonOutput) ) && count($strJsonColumn) != count($strJsonOutput)  )
        {
            $objReturnTransaction->Result->Success = false;
            $objReturnTransaction->Result->Count = 0;
            $objReturnTransaction->Result->Message = 'JSON Column or Output need to be equal length arrays or both strings';
            $objReturnTransaction->Result->Trace = trace();

            return $objReturnTransaction;
        }

        $objQueryResultArray = self::getSimple($strMySqlQuery, $strColumnSort);

        if ( $objQueryResultArray->Result->Success != true || $objQueryResultArray->Result->Count == 0 )
        {
            return $objQueryResultArray;
        }

        foreach ( $objQueryResultArray->Data as $key => $da )
        {
            if ( !empty($strJsonOutput) && !is_array($strJsonOutput) )
            {
                $objJsonDecodedObject = json_decode($da->$strJsonColumn, true);

                $objJsonTransaction = new ExcellTransaction();
                $objJsonTransaction->Data = $objJsonDecodedObject;

                $objUnBase64Data = self::unBase64Encode($objJsonTransaction);

                if ($objUnBase64Data->Result->Success === true)
                {
                    $objQueryResultArray->Data->$key->$strJsonOutput = $objUnBase64Data->Data;
                }
                else
                {
                    $objQueryResultArray->Data->$key->$strJsonOutput = null;
                }

                if ($strJsonColumn != $strJsonOutput)
                {
                    unset($objQueryResultArray->Data->$key->$strJsonColumn);
                }
            }
            elseif (!empty($strJsonOutput))
            {
                $intJsonColumnCount = count($strJsonOutput);

                for ( $currJsonColumnIndex = 0; $currJsonColumnIndex < $intJsonColumnCount; $currJsonColumnIndex++ )
                {
                    $objJsonDecodedObject = json_decode($da[$strJsonColumn[$currJsonColumnIndex]], true);

                    $objJsonTransaction = new ExcellTransaction();
                    $objJsonTransaction->Data = $objJsonDecodedObject;

                    $objUnBase64Data = self::unBase64Encode($objJsonTransaction);

                    if ($objUnBase64Data->Result->Success === true)
                    {
                        $objQueryResultArray->Data->$key->$strJsonOutput = $objUnBase64Data->Data;
                    }
                    else
                    {
                        $objQueryResultArray->Data->$key->$strJsonOutput = null;
                    }

                    if ($strJsonColumn != $strJsonOutput)
                    {
                        unset($objQueryResultArray->Data->$key->$strJsonColumn[$currJsonColumnIndex]);
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
            $objReturnTransaction->Result->Success = false;
            $objReturnTransaction->Result->Count = 0;
            $objReturnTransaction->Result->Message = 'Empty query string passed in';
            $objReturnTransaction->Result->Trace = trace();

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

                $objReturnTransaction->Result->Success = false;
                $objReturnTransaction->Result->Count = 0;
                $objReturnTransaction->Result->Message = 'There was an error connecting to the database [' . $strUpdateError . ']';
                $objReturnTransaction->Result->Trace = trace();

                logText("Database.Update.Error.log", 'There was an error connecting to the database [' . $strUpdateError . ']');

                return $objReturnTransaction;
            }

            $objQueryResult = null;
            $tmStart = microtime(true);

            if ( ! $objQueryResult = $objZgXlDb->query($strMySqlQuery) )
            {
                $strUpdateError = $objZgXlDb->error;

                //$objZgXlDb->close();

                $objReturnTransaction->Result->Success = false;
                $objReturnTransaction->Result->Count = 0;
                $objReturnTransaction->Result->Message = 'There was an error updating the database [' . $strUpdateError . ']';
                $objReturnTransaction->Result->Query = $strMySqlQuery;
                $objReturnTransaction->Result->Trace = trace();

                logText("Database.Update.Error.log", 'There was an error updating the database [' . $strUpdateError . ']: ' . $strMySqlQuery);

                return $objReturnTransaction;
            }

            $tmEnd = microtime(true) - $tmStart;

            static::$arQueries[] = new QueryTracker($strMySqlQuery, $tmEnd, traceArray());

            //$objZgXlDb->close();

            $objReturnTransaction->Result->Success = true;
            $objReturnTransaction->Result->Count = 1;
            $objReturnTransaction->Result->Message = "This query ran successfully";
            $objReturnTransaction->Result->Query = $strMySqlQuery;
            $objReturnTransaction->Result->Trace = trace();

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

            $objReturnTransaction->Result->Success = false;
            $objReturnTransaction->Result->Count = 0;
            $objReturnTransaction->Result->Message = "An error has occurred [". $strUpdateError1 . "]: " . $strUpdateError2;
            $objReturnTransaction->Result->Query = $strMySqlQuery;
            $objReturnTransaction->Result->Trace = trace();

            logText("Database.Update.Error.log", "An error has occurred [". $strUpdateError1 . "]: " . $strUpdateError2 . " " . $strMySqlQuery);

            return $objReturnTransaction;
        }
    }

    public static function getNextEntityId($strDatabaseTable, $strEntityPrimary) : int
    {
        $query = "SELECT `" . $strEntityPrimary . "` FROM `" . $strDatabaseTable . "` ORDER BY `" . $strEntityPrimary . "` DESC LIMIT 1;";
        $objNextId = static::getSimple($query);

        if ( $objNextId->Result->Count === 0)
        {
            return 1000;
        }

        return ($objNextId->Data->First()->{$strEntityPrimary} + 1);
    }

    public static function unBase64Encode(ExcellTransaction $ay) : ExcellTransaction
    {
        $objReturnTransaction = new ExcellTransaction();

        $temp_array =  new stdClass();

        if ( is_array($ay->Data) || $ay->Data instanceof \stdClass )
        {
            foreach ($ay->Data as $ky => $dy )
            {
                if ( is_array($dy) || $dy instanceof \stdClass )
                {
                    $objArrayTransaction = new ExcellTransaction();
                    $objArrayTransaction->Data = $dy;

                    $temp_array->{$ky} = self::unBase64Encode($objArrayTransaction)->Data;
                }
                else
                {
                    $temp_array->{$ky} = base64_decode($dy);
                }
            }

            $objReturnTransaction->Data = $temp_array;
        }
        else
        {
            $objReturnTransaction->Result->Success = false;
            $objReturnTransaction->Result->Message = 'This Datafield was not an array.';
            $objReturnTransaction->Result->Count = 0;
            $objReturnTransaction->Data = null;
        }

        return $objReturnTransaction;
    }

    public static function base64Encode(ExcellTransaction $ay, $intDepth = 0) : ExcellTransaction
    {
        $objReturnTransaction = new ExcellTransaction();

        $temp_array =  array();

        if ( is_array($ay->Data) )
        {
            foreach ($ay->Data as $ky => $dy )
            {
                if ( is_array($dy) )
                {
                    $objArrayTransaction = new ExcellTransaction();
                    $objArrayTransaction->Data = $dy;

                    $temp_array[$ky] = self::base64Encode($objArrayTransaction, $intDepth + 1)->Data;
                }
                else
                {
                    $temp_array[$ky] = base64_encode($dy);
                }
            }

            $objReturnTransaction->Data = $temp_array;
        }
        else
        {
            $objReturnTransaction->Result->Success = false;
            $objReturnTransaction->Result->Message = 'This Datafield was not an array.';
            $objReturnTransaction->Result->Count = 0;
            $objReturnTransaction->Data = null;
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
