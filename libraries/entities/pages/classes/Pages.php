<?php
/**
 * SHELL _site_core Extention for zgWeb.Solutions Web.CMS.App
 */

namespace Entities\Pages\Classes;

use App\Core\AppEntity;
use App\Utilities\Database;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Pages\Models\PageBlockModel;
use Entities\Pages\Models\PageModel;

class Pages extends AppEntity
{
    public $strEntityName       = "pages";
    public $strDatabaseTable    = "page";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = PageModel::class;
    public $strMainModelPrimary = "page_id";
    public $isPrimaryModule     = true;

    public function getById($intEntityRowId) : ExcellTransaction
    {
        $objPageRequest = parent::getById($intEntityRowId);

        if ( $objPageRequest->Result->Success == false || $objPageRequest->Result->Count == 0 )
        {
            return $objPageRequest;
        }

        $arPageBlockPages = array();

        foreach($objPageRequest->Data as $currKey => $currData)
        {
            $arPageBlockPages[] = $this->strMainModelPrimary . " = " . $currData->page_id;
        }

        $strPageBlockQuery = "SELECT * FROM page_block WHERE " . implode(" || ",$arPageBlockPages) . " ORDER BY sort_order ASC";

        $objPageBlockResult = Database::getComplex($strPageBlockQuery,'block_data','block_data','page_block_id');

        if ( $objPageBlockResult->Result->Success == false || $objPageBlockResult->Result->Count == 0 )
        {
            return $objPageRequest;
        }

        $objPageBlocks = array();

        foreach ($objPageBlockResult->Data as $currKey => $currData)
        {
            // TODO - FIX
            $objPageBlocks["PageBlocks"][] = new PageBlockModel($currData);
        }

        $objPageRequest->Data[0]->ChildEntities = $objPageBlocks;

        $objTransactionResult = new ExcellTransaction();

        $objTransactionResult->Result->Success = true;
        $objTransactionResult->Result->Count = $objPageRequest->Data->Count();
        $objTransactionResult->Result->Message = "This Query Returned " . $objPageRequest->Data->Count() . " Results.";
        $objTransactionResult->Data =  $objPageRequest->Data;

        return $objTransactionResult;
    }

    public function GetAllActivePages() : ExcellTransaction
    {
        $strGetAllActivePagesQuery = "SELECT page_id, page_parent_id, status, unique_url, uri_request_list, title, type, menu_order, menu_visibility, menu_name, ddr_widget, page_data 
                                      FROM `page` 
                                      WHERE 
                                        status = 'published' AND 
                                        ( type = 'page' OR type = 'link' OR type = 'admin' OR type LIKE 'dynamic%' )";

        $objAllPagesResult = Database::getComplex($strGetAllActivePagesQuery,'page_data','page_data','page_id');

        if ( $objAllPagesResult->Result->Success === false || $objAllPagesResult->Result->Count === 0 )
        {
            return $objAllPagesResult;
        }

        $objAllPages = array();

        foreach($objAllPagesResult->Data as $currKey => $currData)
        {
            $objAllPages[] = new PageModel($currData);
        }

        $objTransactionResult = new ExcellTransaction();

        $objTransactionResult->Result->Success = true;
        $objTransactionResult->Result->Count = count($objAllPages);
        $objTransactionResult->Result->Message = "This Query Returned " . count($objAllPages) . " Results.";
        $objTransactionResult->Data =  $objAllPages;

        return $objTransactionResult;
    }
}
