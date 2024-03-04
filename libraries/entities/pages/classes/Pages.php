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
    public string $strEntityName       = "pages";
    public $strDatabaseTable    = "page";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = PageModel::class;
    public $strMainModelPrimary = "page_id";
    public $isPrimaryModule     = true;

    public function getById($intEntityRowId) : ExcellTransaction
    {
        $objPageRequest = parent::getById($intEntityRowId);

        if ( $objPageRequest->result->Success == false || $objPageRequest->result->Count == 0 )
        {
            return $objPageRequest;
        }

        $arPageBlockPages = array();

        foreach($objPageRequest->data as $currKey => $currData)
        {
            $arPageBlockPages[] = $this->strMainModelPrimary . " = " . $currData->page_id;
        }

        $strPageBlockQuery = "SELECT * FROM page_block WHERE " . implode(" || ",$arPageBlockPages) . " ORDER BY sort_order ASC";

        $objPageBlockResult = Database::getComplex($strPageBlockQuery,'block_data','block_data','page_block_id');

        if ( $objPageBlockResult->result->Success == false || $objPageBlockResult->result->Count == 0 )
        {
            return $objPageRequest;
        }

        $objPageBlocks = array();

        foreach ($objPageBlockResult->data as $currKey => $currData)
        {
            // TODO - FIX
            $objPageBlocks["PageBlocks"][] = new PageBlockModel($currData);
        }

        $objPageRequest->data[0]->ChildEntities = $objPageBlocks;

        $objTransactionResult = new ExcellTransaction();

        $objTransactionResult->result->Success = true;
        $objTransactionResult->result->Count = $objPageRequest->getData()->Count();
        $objTransactionResult->result->Message = "This Query Returned " . $objPageRequest->getData()->Count() . " Results.";
        $objTransactionResult->data =  $objPageRequest->getData();

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

        if ( $objAllPagesResult->result->Success === false || $objAllPagesResult->result->Count === 0 )
        {
            return $objAllPagesResult;
        }

        $objAllPages = array();

        foreach($objAllPagesResult->data as $currKey => $currData)
        {
            $objAllPages[] = new PageModel($currData);
        }

        $objTransactionResult = new ExcellTransaction();

        $objTransactionResult->result->Success = true;
        $objTransactionResult->result->Count = count($objAllPages);
        $objTransactionResult->result->Message = "This Query Returned " . count($objAllPages) . " Results.";
        $objTransactionResult->data =  $objAllPages;

        return $objTransactionResult;
    }
}
