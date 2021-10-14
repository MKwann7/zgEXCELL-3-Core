<?php

namespace Entities\Cards\Classes;

use App\Core\AppEntity;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Cards\Classes\Cards;
use Entities\Cards\Models\CardModel;
use Entities\Cards\Models\CardPageArchiveModel;
use Entities\Cards\Models\CardPageModel;
use Entities\Cards\Models\CardPageRelModel;

class CardPageArchives extends AppEntity
{
    public $strEntityName       = "Cards";
    public $strDatabaseTable    = "card_tab_archive";
    public $strDatabaseName     = "Archive";
    public $strMainModelName    = CardPageArchiveModel::class;
    public $strMainModelPrimary = "card_tab_archive_id";

    public function CreateNewFromCardPage(CardPageModel $objEntityData) : ExcellTransaction
    {
        $objCardPageArchive = new CardPageArchiveModel();

        $objCardPageArchive->card_tab_id = $objEntityData->card_tab_id;
        $objCardPageArchive->user_id = $objEntityData->user_id;
        $objCardPageArchive->division_id = $objEntityData->division_id;
        $objCardPageArchive->company_id = $objEntityData->company_id;
        $objCardPageArchive->card_tab_type_id = $objEntityData->card_tab_type_id__value ?? $objEntityData->card_tab_type_id ?? 1;
        $objCardPageArchive->title = $objEntityData->title;
        $objCardPageArchive->content = $objEntityData->content;
        $objCardPageArchive->order_number = $objEntityData->order_number;
        $objCardPageArchive->url = $objEntityData->url;
        $objCardPageArchive->library_tab = $objEntityData->library_tab;
        $objCardPageArchive->visibility = $objEntityData->visibility;
        $objCardPageArchive->permanent = $objEntityData->permanent;
        $objCardPageArchive->instance_count = $objEntityData->instance_count;
        $objCardPageArchive->card_tab_data = $objEntityData->card_tab_data;
        $objCardPageArchive->created_on = $objEntityData->created_on;
        $objCardPageArchive->created_by = $objEntityData->created_by;
        $objCardPageArchive->last_updated = $objEntityData->last_updated;
        $objCardPageArchive->updated_by = $objEntityData->updated_by;
        $objCardPageArchive->sys_row_id = $objEntityData->sys_row_id;

        return $this->createNew($objCardPageArchive);
    }

    public function backupExistingCardPagesFromCard(CardModel $objCard) : ExcellTransaction
    {
        $objCardPageRels = new CardPageRels();
        $cardPageRelResult = $objCardPageRels->getWhere(["card_id" => $objCard->card_id]);

        if ($cardPageRelResult->Result->Count === 0)
        {
            return new ExcellTransaction(false, $cardPageRelResult->Result->Message);
        }

        $objCardPage = new CardPage();
        $cardPageResult = $objCardPage->getWhereIn("card_tab_id", $cardPageRelResult->Data->FieldsToArray("card_tab_id"));

        $arCardPageArchiveIds = [];

        foreach($cardPageResult->Data as $currTabsIndex => $currCardPage)
        {
            $objCardPageArchiveResult = $this->CreateNewFromCardPage($currCardPage);

            if ($objCardPageArchiveResult->Result->Success === false)
            {
                $result = $this->UndoCardPageArchive($arCardPageArchiveIds, $objCardPageArchiveResult);
                if ($result->Result->Success === false)
                {
                    return $result;
                }
            }

            $arCardPageArchiveIds[] = $objCardPageArchiveResult->Data->First()->card_tab_archive_id;
        }

        return new ExcellTransaction(true, "Card {$objCard->card_id} tabs were all backed up.", $arCardPageArchiveIds);
    }

    public function backupExistingCardPagesFromCardId(int $intCardId) : ExcellTransaction
    {
        $objTransactionResult = new ExcellTransaction();

        $objCardModule = new Cards();
        $objCardResult = $objCardModule->getById($intCardId);

        if ($objCardResult->Result->Success === false)
        {
            $objTransactionResult->Result->Success = false;
            $objTransactionResult->Result->Message = "Card {$intCardId} not found in database.";

            return $objTransactionResult;
        }

        $objCard = $objCardResult->Data->First();

        return $this->backupExistingCardPagesFromCard($objCard);
    }

    public function backupExistingCardPagesFromCardNum($intCardNum) : ExcellTransaction
    {
        $objTransactionResult = new ExcellTransaction();

        $objCardModule = new Cards();
        $objCardResult = $objCardModule->getWhere(["card_num" => $intCardNum]);

        if ($objCardResult->Result->Success === false)
        {
            $objTransactionResult->Result->Success = false;
            $objTransactionResult->Result->Message = "Card with card_num {$intCardNum} not found in database.";

            return $objTransactionResult;
        }

        $objCard = $objCardResult->Data->First();

        return $this->backupExistingCardPagesFromCard($objCard);
    }

    protected function UndoCardPageArchive($arCardPageArchiveIds, $objCardPageArchiveResult) : ExcellTransaction
    {
        $objTransactionResult = new ExcellTransaction();

        $this->deleteWhere(["card_tab_archive_id", "IN", $arCardPageArchiveIds]);

        $objTransactionResult->Result->Success = false;
        $objTransactionResult->Result->Count = count($arCardPageArchiveIds);
        $objTransactionResult->Result->Message = $objCardPageArchiveResult->Result->Message;
        $objTransactionResult->Result->Query = $objCardPageArchiveResult->Result->Query;

        return $objTransactionResult;
    }
}
