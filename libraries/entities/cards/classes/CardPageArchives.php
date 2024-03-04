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
    public string $strEntityName       = "Cards";
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

        if ($cardPageRelResult->result->Count === 0)
        {
            return new ExcellTransaction(false, $cardPageRelResult->result->Message);
        }

        $objCardPage = new CardPage();
        $cardPageResult = $objCardPage->getWhereIn("card_tab_id", $cardPageRelResult->getData()->FieldsToArray("card_tab_id"));

        $arCardPageArchiveIds = [];

        foreach($cardPageResult->data as $currTabsIndex => $currCardPage)
        {
            $objCardPageArchiveResult = $this->CreateNewFromCardPage($currCardPage);

            if ($objCardPageArchiveResult->result->Success === false)
            {
                $result = $this->UndoCardPageArchive($arCardPageArchiveIds, $objCardPageArchiveResult);
                if ($result->result->Success === false)
                {
                    return $result;
                }
            }

            $arCardPageArchiveIds[] = $objCardPageArchiveResult->getData()->first()->card_tab_archive_id;
        }

        return new ExcellTransaction(true, "Card {$objCard->card_id} tabs were all backed up.", $arCardPageArchiveIds);
    }

    public function backupExistingCardPagesFromCardId(int $intCardId) : ExcellTransaction
    {
        $objTransactionResult = new ExcellTransaction();

        $objCardModule = new Cards();
        $objCardResult = $objCardModule->getById($intCardId);

        if ($objCardResult->result->Success === false)
        {
            $objTransactionResult->result->Success = false;
            $objTransactionResult->result->Message = "Card {$intCardId} not found in database.";

            return $objTransactionResult;
        }

        $objCard = $objCardResult->getData()->first();

        return $this->backupExistingCardPagesFromCard($objCard);
    }

    public function backupExistingCardPagesFromCardNum($intCardNum) : ExcellTransaction
    {
        $objTransactionResult = new ExcellTransaction();

        $objCardModule = new Cards();
        $objCardResult = $objCardModule->getWhere(["card_num" => $intCardNum]);

        if ($objCardResult->result->Success === false)
        {
            $objTransactionResult->result->Success = false;
            $objTransactionResult->result->Message = "Card with card_num {$intCardNum} not found in database.";

            return $objTransactionResult;
        }

        $objCard = $objCardResult->getData()->first();

        return $this->backupExistingCardPagesFromCard($objCard);
    }

    protected function UndoCardPageArchive($arCardPageArchiveIds, $objCardPageArchiveResult) : ExcellTransaction
    {
        $objTransactionResult = new ExcellTransaction();

        $this->deleteWhere(["card_tab_archive_id", "IN", $arCardPageArchiveIds]);

        $objTransactionResult->result->Success = false;
        $objTransactionResult->result->Count = count($arCardPageArchiveIds);
        $objTransactionResult->result->Message = $objCardPageArchiveResult->Result->Message;
        $objTransactionResult->result->Query = $objCardPageArchiveResult->Result->Query;

        return $objTransactionResult;
    }
}
